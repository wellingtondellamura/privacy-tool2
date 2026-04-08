<?php

/**
 * Tests aligned with 006-calculation.feature
 * All rules deterministic: RN-01 to RN-06
 */

use App\Enums\AnswerLevel;
use App\Services\AggregationService;

// RN-01 — Score mapping (canonical enum values)
test('high maps to 100', function () {
    expect(AggregationService::scoreForAnswer(AnswerLevel::HIGH))->toBe(100);
});

test('medium maps to 50', function () {
    expect(AggregationService::scoreForAnswer(AnswerLevel::MEDIUM))->toBe(50);
});

test('low maps to 0', function () {
    expect(AggregationService::scoreForAnswer(AnswerLevel::LOW))->toBe(0);
});

test('other maps to null', function () {
    expect(AggregationService::scoreForAnswer(AnswerLevel::OTHER))->toBeNull();
});

// RN-02 — Category score calculation
test('category score is calculated correctly', function () {
    // 4 questions, total score 300 → round((300/400)*100) = 75
    $scores = [100, 100, 50, 50]; // sum = 300
    expect(AggregationService::categoryScore($scores, 4))->toBe(75);
});

test('category score from feature: 300/400', function () {
    // Scenario: Category score calculation — 4 questions, total 300
    // round((300/400)*100) = round(75) = 75
    $scores = [100, 50, 100, 50]; // sum = 300
    expect(AggregationService::categoryScore($scores, 4))->toBe(75);
});

// RN-03 — Category percentage
test('category percentage calculation', function () {
    expect(AggregationService::categoryPercentage(3, 4))->toBe(75.0);
    expect(AggregationService::categoryPercentage(4, 4))->toBe(100.0);
    expect(AggregationService::categoryPercentage(0, 4))->toBe(0.0);
});

// RN-04 — Section score
test('section score is average of two category scores', function () {
    // Scenario: Section score calculation — cat1=80, cat2=60 → 70
    expect(AggregationService::sectionScore([80, 60]))->toBe(70);
});

test('section score rounds correctly', function () {
    expect(AggregationService::sectionScore([81, 60]))->toBe(71);
    expect(AggregationService::sectionScore([79, 60]))->toBe(70);
});

// RN-05 — Section percentage
test('section percentage is average of two category percentages', function () {
    expect(AggregationService::sectionPercentage([80.0, 60.0]))->toBe(70.0);
});

// RN-06 — Medal assignment
test('score 95 gives gold', function () {
    expect(AggregationService::medalForScore(95))->toBe('gold');
});

test('score 91 gives gold', function () {
    expect(AggregationService::medalForScore(91))->toBe('gold');
});

test('score 100 gives gold', function () {
    expect(AggregationService::medalForScore(100))->toBe('gold');
});

test('score 70 gives silver', function () {
    expect(AggregationService::medalForScore(70))->toBe('silver');
});

test('score 61 gives silver', function () {
    expect(AggregationService::medalForScore(61))->toBe('silver');
});

test('score 90 gives silver', function () {
    expect(AggregationService::medalForScore(90))->toBe('silver');
});

test('score 50 gives bronze', function () {
    expect(AggregationService::medalForScore(50))->toBe('bronze');
});

test('score 41 gives bronze', function () {
    expect(AggregationService::medalForScore(41))->toBe('bronze');
});

test('score 30 gives incipient', function () {
    expect(AggregationService::medalForScore(30))->toBe('incipient');
});

test('score 0 gives incipient', function () {
    expect(AggregationService::medalForScore(0))->toBe('incipient');
});

test('score 40 gives incipient', function () {
    expect(AggregationService::medalForScore(40))->toBe('incipient');
});
