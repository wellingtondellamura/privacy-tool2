<?php

/**
 * Tests aligned with 006-calculation.feature
 * All rules deterministic: RN-01 to RN-06
 */

use App\Services\AggregationService;

// RN-01 — Score mapping
test('Suficiente maps to 100', function () {
    expect(AggregationService::scoreForAnswer('Suficiente'))->toBe(100);
});

test('Insuficiente maps to 50', function () {
    expect(AggregationService::scoreForAnswer('Insuficiente'))->toBe(50);
});

test('Inexistente maps to 0', function () {
    expect(AggregationService::scoreForAnswer('Inexistente'))->toBe(0);
});

test('Apropriado maps to 100', function () {
    expect(AggregationService::scoreForAnswer('Apropriado'))->toBe(100);
});

test('Necessita melhorias maps to 50', function () {
    expect(AggregationService::scoreForAnswer('Necessita melhorias'))->toBe(50);
});

test('Inapropriado maps to 0', function () {
    expect(AggregationService::scoreForAnswer('Inapropriado'))->toBe(0);
});

test('Outro maps to 0', function () {
    expect(AggregationService::scoreForAnswer('Outro'))->toBe(0);
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
    expect(AggregationService::sectionScore(80, 60))->toBe(70);
});

test('section score rounds correctly', function () {
    expect(AggregationService::sectionScore(81, 60))->toBe(71);
    expect(AggregationService::sectionScore(79, 60))->toBe(70);
});

// RN-05 — Section percentage
test('section percentage is average of two category percentages', function () {
    expect(AggregationService::sectionPercentage(80.0, 60.0))->toBe(70.0);
});

// RN-06 — Medal assignment
test('score 95 gives Ouro', function () {
    expect(AggregationService::medalForScore(95))->toBe('Ouro');
});

test('score 91 gives Ouro', function () {
    expect(AggregationService::medalForScore(91))->toBe('Ouro');
});

test('score 100 gives Ouro', function () {
    expect(AggregationService::medalForScore(100))->toBe('Ouro');
});

test('score 70 gives Prata', function () {
    expect(AggregationService::medalForScore(70))->toBe('Prata');
});

test('score 61 gives Prata', function () {
    expect(AggregationService::medalForScore(61))->toBe('Prata');
});

test('score 90 gives Prata', function () {
    expect(AggregationService::medalForScore(90))->toBe('Prata');
});

test('score 50 gives Bronze', function () {
    expect(AggregationService::medalForScore(50))->toBe('Bronze');
});

test('score 41 gives Bronze', function () {
    expect(AggregationService::medalForScore(41))->toBe('Bronze');
});

test('score 30 gives Incipiente', function () {
    expect(AggregationService::medalForScore(30))->toBe('Incipiente');
});

test('score 0 gives Incipiente', function () {
    expect(AggregationService::medalForScore(0))->toBe('Incipiente');
});

test('score 40 gives Incipiente', function () {
    expect(AggregationService::medalForScore(40))->toBe('Incipiente');
});
