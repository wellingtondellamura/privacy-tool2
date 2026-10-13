<?php

namespace App\Services;

class DivergenceService
{
    /**
     * Calculate variance for a set of numeric scores.
     * Variance = (1/N) * Σ(xi - mean)²
     * Null scores ("Não se aplica") are ignored.
     *
     * @param array<int|null> $scores
     */
    public static function variance(array $scores): float
    {
        $scores = array_values(array_filter($scores, fn($s) => $s !== null));
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
     * Standard deviation (same unit as the 0–100 scores).
     *
     * @param array<int|null> $scores
     */
    public static function standardDeviation(array $scores): float
    {
        return sqrt(self::variance($scores));
    }

    /**
     * Classify divergence level based on the standard deviation of the scores.
     * 0–10  → baixa (low)
     * 10–30 → média (medium)   e.g. one evaluator 100, other 50 → σ = 25
     * > 30  → alta (high)      e.g. one evaluator 100, other 0  → σ = 50
     */
    public static function classify(float $standardDeviation): string
    {
        return match (true) {
            $standardDeviation <= 10 => 'low',
            $standardDeviation <= 30 => 'medium',
            default => 'high',
        };
    }

    /**
     * Translate a divergence key to the current locale.
     */
    public static function classifyLabel(string $classification): string
    {
        return __('labels.divergence.' . $classification);
    }

    /**
     * Calculate divergence data for a single question across multiple users.
     *
     * @param array<int|null> $scores  Numeric scores from each user for this question
     * @return array{variance: float, std_dev: float, classification: string}
     */
    public static function forQuestion(array $scores): array
    {
        $variance = self::variance($scores);
        $stdDev = sqrt($variance);

        return [
            'variance' => round($variance, 2),
            'std_dev' => round($stdDev, 2),
            'classification' => self::classify($stdDev),
        ];
    }
}
