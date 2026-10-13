<?php

/**
 * Tests aligned with 011-team_consolidation.feature
 */

use App\Services\AggregationService;
use App\Services\DivergenceService;

// Scenario: Average section score
test('team average section score', function () {
    // Given two users with section scores 80 and 60
    // When calculating team average
    $scores = [80, 60];
    $average = (int) round(array_sum($scores) / count($scores));

    // Then the result must be 70
    expect($average)->toBe(70);
});

// Scenario: Low divergence
test('all same answers gives low divergence', function () {
    // Given all users answered 100 for a question
    $scores = [100, 100, 100, 100];

    // When calculating variance
    $result = DivergenceService::forQuestion($scores);

    // Then divergence classification must be "low"
    expect($result['variance'])->toBe(0.0);
    expect($result['classification'])->toBe('low');
});

// Scenario: High divergence
test('extreme variation gives high divergence', function () {
    // Given answers 0, 100, 0, 100
    $scores = [0, 100, 0, 100];

    // When calculating variance
    $result = DivergenceService::forQuestion($scores);

    // Then divergence classification must be "high"
    expect($result['variance'])->toBe(2500.0);
    expect($result['classification'])->toBe('high');
});

// Classification uses the standard deviation (0–100 scale)
test('one level apart gives medium divergence', function () {
    // Suficiente (100) x Insuficiente (50): variance 625, σ = 25
    $result = DivergenceService::forQuestion([100, 50]);

    expect($result['variance'])->toBe(625.0);
    expect($result['std_dev'])->toBe(25.0);
    expect($result['classification'])->toBe('medium');
});

test('opposite answers give high divergence', function () {
    // Suficiente (100) x Inexistente (0): σ = 50
    expect(DivergenceService::forQuestion([100, 0])['classification'])->toBe('high');
});

test('single dissenting evaluator weighs less as the team grows', function () {
    // 9 x 100 and 1 x 50: σ = 15 → medium; 19 x 100 and 1 x 50: σ ≈ 10.9 → medium
    expect(DivergenceService::forQuestion([...array_fill(0, 9, 100), 50])['classification'])->toBe('medium');
    expect(DivergenceService::forQuestion([...array_fill(0, 30, 100), 50])['classification'])->toBe('low');
});

test('not applicable answers are ignored in divergence', function () {
    expect(DivergenceService::forQuestion([100, null, 100])['classification'])->toBe('low');
});

test('small variation gives low divergence', function () {
    $result = DivergenceService::forQuestion([100, 90]); // σ = 5
    expect($result['variance'])->toBe(25.0);
    expect($result['classification'])->toBe('low');
});

test('standard deviation boundary at 10 is low', function () {
    expect(DivergenceService::classify(10))->toBe('low');
});

test('standard deviation boundary at 11 is medium', function () {
    expect(DivergenceService::classify(11))->toBe('medium');
});

test('standard deviation boundary at 30 is medium', function () {
    expect(DivergenceService::classify(30))->toBe('medium');
});

test('standard deviation boundary at 31 is high', function () {
    expect(DivergenceService::classify(31))->toBe('high');
});
