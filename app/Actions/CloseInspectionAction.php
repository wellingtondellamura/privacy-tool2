<?php

namespace App\Actions;

use App\Models\Inspection;
use App\Models\Response;
use App\Models\ResultSnapshot;
use App\Services\AggregationService;
use App\Services\DivergenceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CloseInspectionAction
{
    public function __construct() {}

    /**
     * Close an inspection: calculate individual + consolidated snapshots.
     * RN-07: Calcular resultados individuais, consolidado, persistir snapshots,
     *        alterar status para closed, bloquear edição posterior.
     */
    public function execute(Inspection $inspection): void
    {
        if (!$inspection->isActive()) {
            throw new \InvalidArgumentException('Only active inspections can be closed.');
        }

        $inspection->load('questionnaireVersion.sections.categories.questions');

        // All responses of the inspection, grouped by user and keyed by question
        $responsesByUser = Response::where('inspection_id', $inspection->id)
            ->get()
            ->groupBy('user_id')
            ->map(fn(Collection $responses) => $responses->keyBy('question_id'));

        DB::transaction(function () use ($inspection, $responsesByUser) {
            // Generate individual snapshots
            foreach ($responsesByUser as $userId => $responses) {
                ResultSnapshot::create([
                    'inspection_id' => $inspection->id,
                    'user_id' => $userId,
                    'payload_json' => $this->calculateUserPayload($inspection, $responses),
                ]);
            }

            // Generate consolidated (team) snapshot
            ResultSnapshot::create([
                'inspection_id' => $inspection->id,
                'user_id' => null,
                'payload_json' => $this->calculateConsolidatedPayload($inspection, $responsesByUser),
            ]);

            // Transition to closed
            $inspection->transitionTo('closed');
        });
    }

    /**
     * Calculate the full result payload for a single user.
     *
     * @param Collection<int, Response> $responses  User responses keyed by question_id
     */
    private function calculateUserPayload(Inspection $inspection, Collection $responses): array
    {
        $sections = [];

        foreach ($inspection->questionnaireVersion->sections as $section) {
            $categories = [];

            foreach ($section->categories as $category) {
                $questions = $category->questions;
                $questionData = [];
                $scores = [];
                $answered = 0;
                foreach ($questions as $question) {
                    $response = $responses->get($question->id);

                    if ($response) {
                        $level = $response->answer;
                        $score = AggregationService::scoreForAnswer($level);
                        $scores[] = $score;
                        $answered++;

                        $questionData[] = [
                            'question_id' => $question->id,
                            'level' => $level->value,
                            'score' => $score,
                        ];
                    }
                }

                $totalQuestions = $questions->count();

                $categories[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'score' => AggregationService::categoryScore($scores),
                    'percentage' => AggregationService::categoryPercentage($answered, $totalQuestions),
                    'total_questions' => $totalQuestions,
                    'answered' => $answered,
                    'questions' => $questionData,
                ];
            }

            $sectionScore = AggregationService::sectionScore(array_column($categories, 'score'));

            $sections[] = [
                'id' => $section->id,
                'name' => $section->name,
                'score' => $sectionScore,
                'percentage' => AggregationService::sectionPercentage(array_column($categories, 'percentage')),
                'medal' => AggregationService::medalForScore($sectionScore),
                'categories' => $categories,
            ];
        }

        $globalScore = AggregationService::globalScore(array_column($sections, 'score'));

        return [
            'global_score' => $globalScore,
            'medal' => [
                'name' => AggregationService::medalForScore($globalScore)
            ],
            'sections' => $sections,
        ];
    }

    /**
     * Calculate consolidated team payload with averages and divergence.
     *
     * @param Collection<int, Collection<int, Response>> $responsesByUser
     */
    private function calculateConsolidatedPayload(Inspection $inspection, Collection $responsesByUser): array
    {
        $sections = [];

        foreach ($inspection->questionnaireVersion->sections as $section) {
            $categories = [];

            foreach ($section->categories as $category) {
                // Category score is computed from raw question scores rather than
                // averaging already-rounded per-user category scores (avoids double rounding).
                $questionData = [];
                $avgQuestionScores = [];
                foreach ($category->questions as $question) {
                    $userScores = [];
                    foreach ($responsesByUser as $responses) {
                        $response = $responses->get($question->id);
                        if ($response) {
                            $userScores[] = AggregationService::scoreForAnswer($response->answer);
                        }
                    }

                    // null when every answer was "Não se aplica"
                    $avgQuestionScore = AggregationService::averageScore($userScores);
                    if (count($userScores) > 0) {
                        $avgQuestionScores[] = $avgQuestionScore;
                    }

                    $questionData[] = [
                        'question_id'   => $question->id,
                        'question_text' => $question->text,
                        'score'         => $avgQuestionScore,
                        ...DivergenceService::forQuestion($userScores),
                    ];
                }

                $categories[] = [
                    'id'        => $category->id,
                    'name'      => $category->name,
                    'score'     => AggregationService::categoryScore($avgQuestionScores),
                    'questions' => $questionData,
                ];
            }

            // Section score uses the same formula as the individual payload
            $sectionScore = AggregationService::sectionScore(array_column($categories, 'score'));

            $sections[] = [
                'id' => $section->id,
                'name' => $section->name,
                'score' => $sectionScore,
                'medal' => AggregationService::medalForScore($sectionScore),
                'categories' => $categories,
            ];
        }

        $globalScore = AggregationService::globalScore(array_column($sections, 'score'));

        return [
            'global_score' => $globalScore,
            'medal' => [
                'name' => AggregationService::medalForScore($globalScore)
            ],
            'sections' => $sections,
            'user_count' => $responsesByUser->count(),
        ];
    }
}
