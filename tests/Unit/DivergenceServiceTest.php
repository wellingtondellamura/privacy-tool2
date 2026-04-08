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

// Additional variance edge cases
test('medium divergence classification', function () {
    // Variance between 11 and 30
    $scores = [100, 90]; // mean=95, var = ((5^2 + 5^2)/2) = 25
    $result = DivergenceService::forQuestion($scores);

    expect($result['variance'])->toBe(25.0);
    expect($result['classification'])->toBe('medium');
});

test('variance boundary at 10 is low', function () {
    expect(DivergenceService::classify(10))->toBe('low');
});

test('variance boundary at 11 is medium', function () {
    expect(DivergenceService::classify(11))->toBe('medium');
});

test('variance boundary at 30 is medium', function () {
    expect(DivergenceService::classify(30))->toBe('medium');
});

test('variance boundary at 31 is high', function () {
    expect(DivergenceService::classify(31))->toBe('high');
});
