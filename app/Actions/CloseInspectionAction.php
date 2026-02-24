<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\Inspection;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResultSnapshot;
use App\Models\Section;
use App\Services\AggregationService;
use App\Services\DivergenceService;

class CloseInspectionAction
{
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

        // Get all users who responded
        $userIds = Response::where('inspection_id', $inspection->id)
            ->distinct()
            ->pluck('user_id');

        $individualPayloads = [];

        // Generate individual snapshots
        foreach ($userIds as $userId) {
            $payload = $this->calculateUserPayload($inspection, $userId);
            $individualPayloads[$userId] = $payload;

            ResultSnapshot::create([
                'inspection_id' => $inspection->id,
                'user_id' => $userId,
                'payload_json' => $payload,
            ]);
        }

        // Generate consolidated (team) snapshot
        $consolidatedPayload = $this->calculateConsolidatedPayload(
            $inspection, $individualPayloads
        );

        ResultSnapshot::create([
            'inspection_id' => $inspection->id,
            'user_id' => null,
            'payload_json' => $consolidatedPayload,
        ]);

        // Transition to closed
        $inspection->transitionTo('closed');
    }

    /**
     * Calculate the full result payload for a single user.
     */
    private function calculateUserPayload(Inspection $inspection, int $userId): array
    {
        $sections = [];

        foreach ($inspection->questionnaireVersion->sections as $section) {
            $categories = [];

            foreach ($section->categories as $category) {
                $questions = $category->questions;
                $scores = [];
                $answered = 0;

                foreach ($questions as $question) {
                    $response = Response::where([
                        'inspection_id' => $inspection->id,
                        'question_id' => $question->id,
                        'user_id' => $userId,
                    ])->first();

                    if ($response) {
                        $scores[] = AggregationService::scoreForAnswer($response->answer);
                        $answered++;
                    }
                }

                $totalQuestions = $questions->count();
                $categoryScore = AggregationService::categoryScore($scores, $totalQuestions);
                $categoryPercentage = AggregationService::categoryPercentage($answered, $totalQuestions);

                $categories[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'score' => $categoryScore,
                    'percentage' => $categoryPercentage,
                    'total_questions' => $totalQuestions,
                    'answered' => $answered,
                ];
            }

            $cat1Score = $categories[0]['score'] ?? 0;
            $cat2Score = $categories[1]['score'] ?? 0;
            $cat1Pct = $categories[0]['percentage'] ?? 0;
            $cat2Pct = $categories[1]['percentage'] ?? 0;

            $sectionScore = AggregationService::sectionScore($cat1Score, $cat2Score);
            $sectionPercentage = AggregationService::sectionPercentage($cat1Pct, $cat2Pct);

            $sections[] = [
                'id' => $section->id,
                'name' => $section->name,
                'score' => $sectionScore,
                'percentage' => $sectionPercentage,
                'medal' => AggregationService::medalForScore($sectionScore),
                'categories' => $categories,
            ];
        }

        $globalScore = count($sections) > 0
            ? (int) round(array_sum(array_column($sections, 'score')) / count($sections))
            : 0;

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
     */
    private function calculateConsolidatedPayload(Inspection $inspection, array $individualPayloads): array
    {
        $userCount = count($individualPayloads);
        $sections = [];

        foreach ($inspection->questionnaireVersion->sections as $sIndex => $section) {
            $categories = [];

            foreach ($section->categories as $cIndex => $category) {
                // Average category scores
                $catScores = [];
                foreach ($individualPayloads as $payload) {
                    $catScores[] = $payload['sections'][$sIndex]['categories'][$cIndex]['score'] ?? 0;
                }
                $avgCatScore = $userCount > 0 ? (int) round(array_sum($catScores) / $userCount) : 0;

                // Question-level scores and divergence
                $questionData = [];
                foreach ($category->questions as $question) {
                    $userScoresForQuestion = [];
                    foreach (array_keys($individualPayloads) as $userId) {
                        $response = Response::where([
                            'inspection_id' => $inspection->id,
                            'question_id' => $question->id,
                            'user_id' => $userId,
                        ])->first();

                        if ($response) {
                            $userScoresForQuestion[] = AggregationService::scoreForAnswer($response->answer);
                        }
                    }

                    $avgQuestionScore = count($userScoresForQuestion) > 0
                        ? (int) round(array_sum($userScoresForQuestion) / count($userScoresForQuestion))
                        : 0;

                    $questionData[] = [
                        'question_id' => $question->id,
                        'question_text' => $question->text,
                        'score' => $avgQuestionScore,
                        ...(count($userScoresForQuestion) > 0
                            ? DivergenceService::forQuestion($userScoresForQuestion)
                            : ['variance' => 0, 'classification' => 'baixa']),
                    ];
                }

                $categories[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'score' => $avgCatScore,
                    'questions' => $questionData,
                ];
            }

            // Average section scores
            $sectionScores = [];
            foreach ($individualPayloads as $payload) {
                $sectionScores[] = $payload['sections'][$sIndex]['score'] ?? 0;
            }
            $avgSectionScore = $userCount > 0 ? (int) round(array_sum($sectionScores) / $userCount) : 0;

            $sections[] = [
                'id' => $section->id,
                'name' => $section->name,
                'score' => $avgSectionScore,
                'medal' => AggregationService::medalForScore($avgSectionScore),
                'categories' => $categories,
            ];
        }

        $globalScore = count($sections) > 0
            ? (int) round(array_sum(array_column($sections, 'score')) / count($sections))
            : 0;

        return [
            'global_score' => $globalScore,
            'medal' => [
                'name' => AggregationService::medalForScore($globalScore)
            ],
            'sections' => $sections,
            'user_count' => $userCount,
        ];
    }
}
