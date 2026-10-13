<?php

namespace App\Services;

use App\Models\ResultSnapshot;
use App\Models\RoundSnapshot;

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

        if ($baseline->inspection->project_id !== $comparison->inspection->project_id) {
            throw new \InvalidArgumentException('Both inspections must belong to the same project.');
        }

        return static::comparePayloads($baseline->payload_json, $comparison->payload_json);
    }

    /**
     * Compare two round snapshots from the same project.
     */
    public static function compareRoundSnapshots(RoundSnapshot $baseline, RoundSnapshot $comparison): array
    {
        if ($baseline->evaluationRound->project_id !== $comparison->evaluationRound->project_id) {
            throw new \InvalidArgumentException('Both rounds must belong to the same project.');
        }

        return static::comparePayloads($baseline->payload_json, $comparison->payload_json);
    }

    /**
     * Logic to calculate delta between two payloads.
     * Sections, categories and questions are matched by id (falling back to position
     * for legacy payloads). A null score means "Não se aplica"/no data; the delta is
     * then null as well.
     */
    public static function comparePayloads(array $baselinePayload, array $comparisonPayload): array
    {
        $baselineSections = $baselinePayload['sections'] ?? [];

        $sections = [];

        foreach ($comparisonPayload['sections'] ?? [] as $index => $compSection) {
            $baseSection = static::match($baselineSections, $compSection, 'id', $index);
            $baseCategories = $baseSection['categories'] ?? [];

            $categories = [];
            foreach ($compSection['categories'] ?? [] as $cIndex => $compCat) {
                $baseCat = static::match($baseCategories, $compCat, 'id', $cIndex);
                $baseQuestions = $baseCat['questions'] ?? [];

                $questions = [];
                foreach ($compCat['questions'] ?? [] as $qIndex => $compQuestion) {
                    $baseQuestion = static::match($baseQuestions, $compQuestion, 'question_id', $qIndex);

                    $questions[] = [
                        'question_id' => $compQuestion['question_id'] ?? null,
                        'question_text' => $compQuestion['question_text'] ?? '',
                        ...static::scores($baseQuestion, $compQuestion),
                    ];
                }

                $categories[] = [
                    'id' => $compCat['id'] ?? null,
                    'name' => $compCat['name'] ?? '',
                    ...static::scores($baseCat, $compCat),
                    'questions' => $questions,
                ];
            }

            $sections[] = [
                'id' => $compSection['id'] ?? null,
                'name' => $compSection['name'] ?? '',
                ...static::scores($baseSection, $compSection),
                'categories' => $categories,
            ];
        }

        return ['sections' => $sections];
    }

    private static function scores(?array $base, array $comparison): array
    {
        // A missing counterpart is not a zero: "Não se aplica" and absent items
        // must not invent a delta against 0.
        $baseScore = $base === null ? null : ($base['score'] ?? null);
        $compScore = $comparison['score'] ?? null;

        return [
            'baseline_score' => $baseScore,
            'comparison_score' => $compScore,
            'delta' => ($baseScore === null || $compScore === null) ? null : $compScore - $baseScore,
        ];
    }

    /**
     * Find the baseline counterpart of an item: same id when present in the baseline
     * (same questionnaire version), otherwise the item at the same position.
     */
    private static function match(array $baselineItems, array $item, string $idKey, int|string $index): ?array
    {
        if (isset($item[$idKey])) {
            foreach ($baselineItems as $baselineItem) {
                if (($baselineItem[$idKey] ?? null) === $item[$idKey]) {
                    return $baselineItem;
                }
            }
        }

        return $baselineItems[$index] ?? null;
    }
}
