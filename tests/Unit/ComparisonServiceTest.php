<?php

/**
 * Tests aligned with 010-comparison.feature
 */

use App\Models\ResultSnapshot;
use App\Services\ComparisonService;

test('positive delta between inspections', function () {
    // Scenario: Positive delta
    $baseline = new ResultSnapshot([
        'inspection_id' => 1,
        'user_id' => null,
        'payload_json' => [
            'sections' => [
                ['id' => 1, 'name' => 'Section 1', 'score' => 60, 'categories' => [
                    ['id' => 1, 'name' => 'Cat 1', 'score' => 60, 'questions' => [
                        ['question_id' => 1, 'score' => 60]
                    ]],
                    ['id' => 2, 'name' => 'Cat 2', 'score' => 60],
                ]],
            ],
        ],
    ]);

    $comparison = new ResultSnapshot([
        'inspection_id' => 2,
        'user_id' => null,
        'payload_json' => [
            'sections' => [
                ['id' => 1, 'name' => 'Section 1', 'score' => 80, 'categories' => [
                    ['id' => 1, 'name' => 'Cat 1', 'score' => 80, 'questions' => [
                        ['question_id' => 1, 'score' => 80]
                    ]],
                    ['id' => 2, 'name' => 'Cat 2', 'score' => 80],
                ]],
            ],
        ],
    ]);

    // Mock inspections to be in same project
    $mockInspection = new class { public int $project_id = 1; };
    $baseline->setRelation('inspection', $mockInspection);
    $comparison->setRelation('inspection', $mockInspection);

    $result = ComparisonService::compare($baseline, $comparison);

    // Then delta must be +20
    expect($result['sections'][0]['delta'])->toBe(20);
    expect($result['sections'][0]['baseline_score'])->toBe(60);
    expect($result['sections'][0]['comparison_score'])->toBe(80);

    // Assert question delta
    expect($result['sections'][0]['categories'][0]['questions'][0]['delta'])->toBe(20);
});

test('negative delta between inspections', function () {
    // Scenario: Negative delta
    $baseline = new ResultSnapshot([
        'inspection_id' => 1,
        'user_id' => null,
        'payload_json' => [
            'sections' => [
                ['id' => 1, 'name' => 'Section 1', 'score' => 80, 'categories' => [
                    ['id' => 1, 'name' => 'Cat 1', 'score' => 80],
                ]],
            ],
        ],
    ]);

    $comparison = new ResultSnapshot([
        'inspection_id' => 2,
        'user_id' => null,
        'payload_json' => [
            'sections' => [
                ['id' => 1, 'name' => 'Section 1', 'score' => 60, 'categories' => [
                    ['id' => 1, 'name' => 'Cat 1', 'score' => 60],
                ]],
            ],
        ],
    ]);

    $mockInspection = new class { public int $project_id = 1; };
    $baseline->setRelation('inspection', $mockInspection);
    $comparison->setRelation('inspection', $mockInspection);

    $result = ComparisonService::compare($baseline, $comparison);

    // Then delta must be -20
    expect($result['sections'][0]['delta'])->toBe(-20);
});

test('comparison rejects non-consolidated snapshots', function () {
    $snapshot = new ResultSnapshot([
        'inspection_id' => 1,
        'user_id' => 1, // Not consolidated!
        'payload_json' => ['sections' => []],
    ]);

    expect(fn () => ComparisonService::compare($snapshot, $snapshot))
        ->toThrow(\InvalidArgumentException::class);
});

test('comparison rejects different projects', function () {
    $baseline = new ResultSnapshot([
        'inspection_id' => 1,
        'user_id' => null,
        'payload_json' => ['sections' => []],
    ]);
    $comparison = new ResultSnapshot([
        'inspection_id' => 2,
        'user_id' => null,
        'payload_json' => ['sections' => []],
    ]);

    $mockInsp1 = new class { public int $project_id = 1; };
    $mockInsp2 = new class { public int $project_id = 2; };
    $baseline->setRelation('inspection', $mockInsp1);
    $comparison->setRelation('inspection', $mockInsp2);

    expect(fn () => ComparisonService::compare($baseline, $comparison))
        ->toThrow(\InvalidArgumentException::class);
});
