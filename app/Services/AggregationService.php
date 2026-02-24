<?php

namespace App\Services;

class AggregationService
{
    /**
     * RN-01 — Convert answer to numeric score.
     *
     * Existência e Qualidade:
     *   Suficiente = 100, Insuficiente = 50, Inexistente = 0, Outro = 0
     * Formato:
     *   Apropriado = 100, Necessita melhorias = 50, Inapropriado = 0, Outro = 0
     */
    public static function scoreForAnswer(string $answer): int
    {
        return match ($answer) {
            'Suficiente', 'Apropriado' => 100,
            'Insuficiente', 'Necessita melhorias' => 50,
            'Inexistente', 'Inapropriado', 'Outro' => 0,
            default => 0,
        };
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
     * scoreSecao = round((scoreCat1 + scoreCat2) / 2)
     */
    public static function sectionScore(int $cat1Score, int $cat2Score): int
    {
        return (int) round(($cat1Score + $cat2Score) / 2);
    }

    /**
     * RN-05 — Percentual respondido da seção.
     * percentualSecao = (percentualCat1 + percentualCat2) / 2
     */
    public static function sectionPercentage(float $cat1Pct, float $cat2Pct): float
    {
        return ($cat1Pct + $cat2Pct) / 2;
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
