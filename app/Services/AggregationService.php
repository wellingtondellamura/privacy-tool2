<?php

namespace App\Services;

use App\Enums\AnswerLevel;

class AggregationService
{
    /**
     * RN-01 — Convert answer to numeric score using canonical enum.
     * "Não se aplica" (other) maps to null and is excluded from every average.
     */
    public static function scoreForAnswer(AnswerLevel $answer): ?int
    {
        return AnswerScoreResolver::resolve($answer);
    }

    /**
     * RN-02 — Score da categoria.
     * scoreCategoria = round((sum(pontos) / (totalRespondidasAplicaveis * 100)) * 100)
     *
     * Only applicable answered questions count: null scores ("Não se aplica")
     * and unanswered questions are left out of the denominator (MANUAL §9.4).
     * Returns null when the category has no applicable answer.
     *
     * @param array<int|null> $scores  Scores of the answered questions
     */
    public static function categoryScore(array $scores): ?int
    {
        $applicable = self::applicable($scores);
        if (count($applicable) === 0) {
            return null;
        }

        return (int) round((array_sum($applicable) / (count($applicable) * 100)) * 100);
    }

    /**
     * RN-03 — Percentual respondido da categoria.
     * percentualCategoria = (respondidas / totalPerguntasCategoria) * 100
     */
    public static function categoryPercentage(int $answered, int $totalQuestions): float
    {
        if ($totalQuestions === 0) {
            return 0;
        }

        return ($answered / $totalQuestions) * 100;
    }

    /**
     * RN-04 — Score da seção.
     * scoreSecao = round(sum(categoryScores) / totalCategories)
     * Categories without applicable answers (null) are ignored.
     *
     * @param array<int|null> $categoryScores
     */
    public static function sectionScore(array $categoryScores): ?int
    {
        $applicable = self::applicable($categoryScores);
        if (count($applicable) === 0) {
            return null;
        }

        return (int) round(array_sum($applicable) / count($applicable));
    }

    /**
     * Score global = round(sum(sectionScores) / totalSections), ignoring sections
     * without applicable answers.
     *
     * @param array<int|null> $sectionScores
     */
    public static function globalScore(array $sectionScores): int
    {
        return self::sectionScore($sectionScores) ?? 0;
    }

    /**
     * Average of the applicable scores of a single question across evaluators.
     *
     * @param array<int|null> $scores
     */
    public static function averageScore(array $scores): ?int
    {
        $applicable = self::applicable($scores);
        if (count($applicable) === 0) {
            return null;
        }

        return (int) round(array_sum($applicable) / count($applicable));
    }

    /**
     * RN-05 — Percentual respondido da seção.
     * percentualSecao = sum(categoryPercentages) / totalCategories
     *
     * @param float[] $categoryPercentages
     */
    public static function sectionPercentage(array $categoryPercentages): float
    {
        $count = count($categoryPercentages);
        if ($count === 0) {
            return 0;
        }

        return array_sum($categoryPercentages) / $count;
    }

    /**
     * RN-06 — Medalha por score de seção.
     * 91–100 → Ouro
     * 61–90  → Prata
     * 41–60  → Bronze
     * 0–40   → Incipiente
     * null   → Sem dados (nothing applicable to evaluate)
     */
    public static function medalForScore(?int $score): string
    {
        return match (true) {
            $score === null => 'no_data',
            $score >= 91 => 'gold',
            $score >= 61 => 'silver',
            $score >= 41 => 'bronze',
            default => 'incipient',
        };
    }

    /**
     * Translate a medal key to the current locale.
     */
    public static function medalLabel(string $medal): string
    {
        return __('labels.medals.' . $medal);
    }

    /**
     * @param array<int|null> $scores
     * @return int[]
     */
    private static function applicable(array $scores): array
    {
        return array_values(array_filter($scores, fn($s) => $s !== null));
    }
}
