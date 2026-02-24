<?php

namespace App\Services;

use App\Models\ResultSnapshot;

class ComparisonService
{
    /**
     * Compare two consolidated snapshots from the same project.
     * Calculates delta by section, category, and question.
     *
     * @return array{sections: array}
     * @throws \InvalidArgumentException
     */
    public static function compare(ResultSnapshot $baseline, ResultSnapshot $comparison): array
    {
        if (!$baseline->isConsolidated() || !$comparison->isConsolidated()) {
            throw new \InvalidArgumentException('Both snapshots must be consolidated (team) snapshots.');
        }

        $baselineInspection = $baseline->inspection;
        $comparisonInspection = $comparison->inspection;

        if ($baselineInspection->project_id !== $comparisonInspection->project_id) {
            throw new \InvalidArgumentException('Both inspections must belong to the same project.');
        }

        $baselineSections = $baseline->payload_json['sections'] ?? [];
        $comparisonSections = $comparison->payload_json['sections'] ?? [];

        $sections = [];

        foreach ($comparisonSections as $index => $compSection) {
            $baseSection = $baselineSections[$index] ?? null;
            $baseScore = $baseSection['score'] ?? 0;
            $compScore = $compSection['score'] ?? 0;

            $categories = [];
            $compCategories = $compSection['categories'] ?? [];

            foreach ($compCategories as $cIndex => $compCat) {
                $baseCat = $baseSection['categories'][$cIndex] ?? null;
                $baseCatScore = $baseCat['score'] ?? 0;
                $compCatScore = $compCat['score'] ?? 0;

                $questions = [];
                $compQuestions = $compCat['questions'] ?? [];

                foreach ($compQuestions as $qIndex => $compQuestion) {
                    $baseQuestion = $baseCat['questions'][$qIndex] ?? null;
                    $baseQuestionScore = $baseQuestion['score'] ?? 0;
                    $compQuestionScore = $compQuestion['score'] ?? 0;

                    $questions[] = [
                        'question_id' => $compQuestion['question_id'] ?? null,
                        'question_text' => $compQuestion['question_text'] ?? '',
                        'baseline_score' => $baseQuestionScore,
                        'comparison_score' => $compQuestionScore,
                        'delta' => $compQuestionScore - $baseQuestionScore,
                    ];
                }

                $categories[] = [
                    'id' => $compCat['id'] ?? null,
                    'name' => $compCat['name'] ?? '',
                    'baseline_score' => $baseCatScore,
                    'comparison_score' => $compCatScore,
                    'delta' => $compCatScore - $baseCatScore,
                    'questions' => $questions,
                ];
            }

            $sections[] = [
                'id' => $compSection['id'] ?? null,
                'name' => $compSection['name'] ?? '',
                'baseline_score' => $baseScore,
                'comparison_score' => $compScore,
                'delta' => $compScore - $baseScore,
                'categories' => $categories,
            ];
        }

        return ['sections' => $sections];
    }
}
