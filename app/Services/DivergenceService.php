<?php

namespace App\Services;

class DivergenceService
{
    /**
     * Calculate variance for a set of numeric scores.
     * Variance = (1/N) * Σ(xi - mean)²
     *
     * @param int[] $scores
     */
    public static function variance(array $scores): float
    {
        $n = count($scores);
        if ($n === 0) {
            return 0;
        }

        $mean = array_sum($scores) / $n;
        $sumSquaredDiffs = 0;

        foreach ($scores as $score) {
            $sumSquaredDiffs += ($score - $mean) ** 2;
        }

        return $sumSquaredDiffs / $n;
    }

    /**
     * Classify divergence level based on variance.
     * 0–10  → baixa (low)
     * 11–30 → média (medium)
     * > 30  → alta (high)
     */
    public static function classify(float $variance): string
    {
        return match (true) {
            $variance <= 10 => 'baixa',
            $variance <= 30 => 'média',
            default => 'alta',
        };
    }

    /**
     * Calculate divergence data for a single question across multiple users.
     *
     * @param int[] $scores  Numeric scores from each user for this question
     * @return array{variance: float, classification: string}
     */
    public static function forQuestion(array $scores): array
    {
        $variance = self::variance($scores);

        return [
            'variance' => round($variance, 2),
            'classification' => self::classify($variance),
        ];
    }
}
