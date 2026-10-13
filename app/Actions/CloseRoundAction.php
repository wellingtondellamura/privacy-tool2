<?php

namespace App\Actions;

use App\Models\EvaluationRound;
use App\Models\RoundSnapshot;
use App\Services\AggregationService;
use App\Services\DivergenceService;
use Illuminate\Support\Facades\DB;

class CloseRoundAction
{
    /**
     * Close an evaluation round if all inspections are closed.
     * Generates a RoundSnapshot by averaging individual inspection consolidated snapshots.
     */
    public function execute(EvaluationRound $round): bool
    {
        // 1. Load ONLY closed inspections with their consolidated snapshots (user_id IS NULL)
        $inspections = $round->inspections()
            ->where('status', 'closed')
            ->with(['resultSnapshots' => function ($query) {
                $query->whereNull('user_id');
            }])->get();

        if ($inspections->isEmpty()) {
            return false;
        }

        $round->loadMissing('project');

        return DB::transaction(function () use ($round, $inspections) {
            $roundPayload = $this->calculateRoundPayload($inspections, $round);

            RoundSnapshot::create([
                'evaluation_round_id' => $round->id,
                'payload_json' => $roundPayload,
            ]);

            $round->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            return true;
        });
    }

    public function calculatePreviewPayload(EvaluationRound $round): array
    {
        $inspections = $round->inspections()
            ->where('status', 'closed')
            ->with(['resultSnapshots' => function ($query) {
                $query->whereNull('user_id');
            }])->get();

        if ($inspections->isEmpty()) {
            return ['global_score' => 0, 'medal' => ['name' => 'no_data'], 'sections' => []];
        }

        $round->loadMissing('project');

        return $this->calculateRoundPayload($inspections, $round);
    }

    /**
     * Merge multiple inspection consolidated payloads into one round payload.
     */
    public function calculateRoundPayload($inspections, EvaluationRound $round): array
    {
        $payloads = $inspections->map(fn($i) => $i->resultSnapshots->first()?->payload_json)->filter();
        $count = $payloads->count();

        if ($count === 0) {
            return ['global_score' => 0, 'medal' => ['name' => 'incipient'], 'sections' => []];
        }

        // We assume all inspections in a round use the same questionnaire version structure.
        // We use the first one as template for structure.
        $template = $payloads->first();
        $sections = [];

        // Fetch consensus strategies
        $consensusModel = $round->project->consensus_model;
        if (!$consensusModel instanceof \App\Enums\ConsensusModel) {
            $consensusModel = \App\Enums\ConsensusModel::tryFrom($consensusModel) ?? \App\Enums\ConsensusModel::OWNER_DECIDES;
        }
        $resolvedAnswers = $consensusModel->strategy()->resolve($round);

        // Index every inspection payload by question_id, so questions are matched by
        // identity instead of position. Null = unanswered or "Não se aplica".
        $scoresByQuestion = [];
        foreach ($payloads as $payload) {
            foreach ($payload['sections'] ?? [] as $section) {
                foreach ($section['categories'] ?? [] as $category) {
                    foreach ($category['questions'] ?? [] as $question) {
                        $scoresByQuestion[$question['question_id']][] = $question['score'] ?? null;
                    }
                }
            }
        }

        foreach ($template['sections'] as $templateSection) {
            $categories = [];
            foreach ($templateSection['categories'] as $templateCategory) {

                // Average question scores or use resolved consensus scores
                $questions = [];
                foreach ($templateCategory['questions'] as $templateQuestion) {
                    $qId = $templateQuestion['question_id'];
                    $qScores = $scoresByQuestion[$qId] ?? [];
                    $divergence = DivergenceService::forQuestion($qScores);

                    if (isset($resolvedAnswers[$qId])) {
                        $answerLevel = \App\Enums\AnswerLevel::tryFrom($resolvedAnswers[$qId]) ?? \App\Enums\AnswerLevel::OTHER;
                        $qScore = $answerLevel->score();
                        $levelValue = $answerLevel->value;
                    } else {
                        $qScore = AggregationService::averageScore($qScores);
                        $levelValue = match (true) {
                            $qScore === null => 'other',
                            $qScore >= 91 => 'high',
                            $qScore >= 41 => 'medium',
                            default => 'low',
                        };
                    }

                    $questions[] = [
                        'question_id'   => $qId,
                        'question_text' => $templateQuestion['question_text'] ?? '',
                        'level'         => $levelValue,
                        'score'         => $qScore,
                        'variance'      => $divergence['variance'],
                        'std_dev'       => $divergence['std_dev'],
                        'classification' => $divergence['classification'],
                    ];
                }

                $categories[] = [
                    'id' => $templateCategory['id'],
                    'name' => $templateCategory['name'],
                    // Recalculate category score from the finalized question scores
                    'score' => AggregationService::categoryScore(array_column($questions, 'score')),
                    'questions' => $questions,
                ];
            }

            // Recalculate section score based on category scores
            $secScores = array_column($categories, 'score');
            $avgSecScore = AggregationService::sectionScore($secScores);

            $sections[] = [
                'id' => $templateSection['id'],
                'name' => $templateSection['name'],
                'score' => $avgSecScore,
                'medal' => AggregationService::medalForScore($avgSecScore),
                'categories' => $categories,
            ];
        }

        // Bug 1 fix: calculate global_score as average of section scores (same formula as CloseInspectionAction),
        // not as average of individual inspection global_scores (which would be averaging already-rounded averages).
        $globalScore = AggregationService::globalScore(array_column($sections, 'score'));

        return [
            'global_score' => $globalScore,
            'medal' => [
                'name' => AggregationService::medalForScore($globalScore)
            ],
            'sections' => $sections,
            'inspection_count' => $count,
            'user_count_total' => $payloads->sum('user_count'),
            'individual_scores' => $payloads->pluck('global_score')->shuffle()->toArray(),
            'is_self_assessment' => $round->project->is_self_assessment,
            'software_version' => $round->software_version,
            'closed_at' => $round->closed_at ? $round->closed_at->toIso8601String() : now()->toIso8601String(),
        ];
    }
}
