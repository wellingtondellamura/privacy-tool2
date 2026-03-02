<?php

namespace App\Actions;

use App\Models\EvaluationRound;
use App\Models\RoundSnapshot;
use App\Services\AggregationService;
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

        return DB::transaction(function () use ($round, $inspections) {
            $roundPayload = $this->calculateRoundPayload($inspections);

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
            return ['global_score' => 0, 'medal' => ['name' => 'Sem Dados'], 'sections' => []];
        }

        return $this->calculateRoundPayload($inspections);
    }

    /**
     * Merge multiple inspection consolidated payloads into one round payload.
     */
    public function calculateRoundPayload($inspections): array
    {
        $payloads = $inspections->map(fn($i) => $i->resultSnapshots->first()?->payload_json)->filter();
        $count = $payloads->count();

        if ($count === 0) {
            return ['global_score' => 0, 'medal' => ['name' => 'Incipiente'], 'sections' => []];
        }

        // We assume all inspections in a round use the same questionnaire version structure.
        // We use the first one as template for structure.
        $template = $payloads->first();
        $sections = [];

        foreach ($template['sections'] as $sIndex => $templateSection) {
            $categories = [];
            foreach ($templateSection['categories'] as $cIndex => $templateCategory) {
                
                // Average category scores across all inspections
                $catScores = $payloads->map(fn($p) => $p['sections'][$sIndex]['categories'][$cIndex]['score'] ?? 0);
                $avgCatScore = (int) round($catScores->sum() / $count);

                // Average question scores
                $questions = [];
                foreach ($templateCategory['questions'] as $qIndex => $templateQuestion) {
                    $qScores = $payloads->map(fn($p) => $p['sections'][$sIndex]['categories'][$cIndex]['questions'][$qIndex]['score'] ?? 0);
                    $avgQScores = (int) round($qScores->sum() / $count);
                    
                    $levelValue = match (true) {
                        $avgQScores >= 91 => 'high',
                        $avgQScores >= 41 => 'medium',
                        default => 'low',
                    };

                    $questions[] = [
                        'question_id' => $templateQuestion['question_id'],
                        'level' => $levelValue,
                        'score' => $avgQScores,
                        'variance' => 0, 
                        'classification' => 'baixa',
                    ];
                }

                $categories[] = [
                    'id' => $templateCategory['id'],
                    'name' => $templateCategory['name'],
                    'score' => $avgCatScore,
                    'questions' => $questions,
                ];
            }

            // Average section scores
            $secScores = $payloads->map(fn($p) => $p['sections'][$sIndex]['score'] ?? 0);
            $avgSecScore = (int) round($secScores->sum() / $count);

            $sections[] = [
                'id' => $templateSection['id'],
                'name' => $templateSection['name'],
                'score' => $avgSecScore,
                'medal' => AggregationService::medalForScore($avgSecScore),
                'categories' => $categories,
            ];
        }

        $globalScore = (int) round($payloads->sum('global_score') / $count);

        return [
            'global_score' => $globalScore,
            'medal' => [
                'name' => AggregationService::medalForScore($globalScore)
            ],
            'sections' => $sections,
            'inspection_count' => $count,
            'user_count_total' => $payloads->sum('user_count'),
            'individual_scores' => $payloads->pluck('global_score')->shuffle()->toArray(),
        ];
    }
}
