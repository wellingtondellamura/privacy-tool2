<?php

namespace App\Services;

use App\Enums\AnswerLevel;

class AggregationService
{
    /**
     * RN-01 — Convert answer to numeric score using canonical enum.
     */
    public static function scoreForAnswer(AnswerLevel $answer): ?int
    {
        return AnswerScoreResolver::resolve($answer);
    }

    /**
     * RN-02 — Score da categoria.
     * scoreCategoria = round((sum(pontos) / (totalPerguntasCategoria * 100)) * 100)
     *
     * @param int[] $scores  Array of numeric scores for each answered question
     * @param int   $totalQuestions  Total number of questions in the category
     */
    public static function categoryScore(array $scores, int $totalQuestions): int
    {
        if ($totalQuestions === 0) {
            return 0;
        }

        $sum = array_sum($scores);
        return (int) round(($sum / ($totalQuestions * 100)) * 100);
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
     *
     * @param int[] $categoryScores
     */
    public static function sectionScore(array $categoryScores): int
    {
        $count = count($categoryScores);
        if ($count === 0) {
            return 0;
        }

        return (int) round(array_sum($categoryScores) / $count);
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
     */
    public static function medalForScore(int $score): string
    {
        return match (true) {
            $score >= 91 => 'Ouro',
            $score >= 61 => 'Prata',
            $score >= 41 => 'Bronze',
            default => 'Incipiente',
        };
    }
}
