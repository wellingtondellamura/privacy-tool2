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

        foreach ($template['sections'] as $sIndex => $templateSection) {
            $categories = [];
            foreach ($templateSection['categories'] as $cIndex => $templateCategory) {
                
                // Average question scores or use resolved consensus scores
                $questions = [];
                foreach ($templateCategory['questions'] as $qIndex => $templateQuestion) {
                    $qId = $templateQuestion['question_id'];
                    $qScores = $payloads->map(fn($p) => $p['sections'][$sIndex]['categories'][$cIndex]['questions'][$qIndex]['score'] ?? 0);
                    $divergence = DivergenceService::forQuestion($qScores->toArray());

                    if (isset($resolvedAnswers[$qId])) {
                        $ansVal = $resolvedAnswers[$qId];
                        $answerLevel = \App\Enums\AnswerLevel::tryFrom($ansVal) ?? \App\Enums\AnswerLevel::OTHER;
                        $qScore = $answerLevel->score() ?? 0;
                        $levelValue = $answerLevel->value;
                    } else {
                        $qScore = (int) round($qScores->sum() / $count);
                        $levelValue = match (true) {
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
                        'classification' => $divergence['classification'],
                    ];
                }

                // Recalculate category score dynamically based on the finalized question scores
                $catQuestionScores = array_filter(array_column($questions, 'score'), fn($s) => $s !== null);
                $avgCatScore = AggregationService::categoryScore($catQuestionScores, count($questions));

                $categories[] = [
                    'id' => $templateCategory['id'],
                    'name' => $templateCategory['name'],
                    'score' => $avgCatScore,
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
        $globalScore = count($sections) > 0
            ? (int) round(array_sum(array_column($sections, 'score')) / count($sections))
            : 0;

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
