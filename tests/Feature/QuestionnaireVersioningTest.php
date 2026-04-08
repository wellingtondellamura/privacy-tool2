<?php

/**
 * Tests aligned with 005-questionnaire_versioning.feature
 */

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\Section;
use Database\Seeders\QuestionnaireV1Seeder;

beforeEach(function () {
    $this->seed(QuestionnaireV1Seeder::class);
});

test('V1 seeder creates correct structure', function () {
    $version = QuestionnaireVersion::where('version_number', 1)->first();

    expect($version)->not->toBeNull();
    expect($version->is_active)->toBeTrue();

    // 2 sections
    expect($version->sections)->toHaveCount(2);

    // Each section has 5 categories
    foreach ($version->sections as $section) {
        expect($section->categories)->toHaveCount(5);
    }

    // Total of 46 questions
    $totalQuestions = Question::count();
    expect($totalQuestions)->toBe(46);
});

test('question counts per section are correct', function () {
    $version = QuestionnaireVersion::where('version_number', 1)->first();
    $sections = $version->sections;

    $expectedCounts = [
        'Existência e Qualidade da Informação' => 26,
        'Formato de Apresentação' => 20,
    ];

    foreach ($sections as $section) {
        $questionCount = $section->questions()->count();
        expect($questionCount)->toBe($expectedCounts[$section->name],
            "Section '{$section->name}' should have {$expectedCounts[$section->name]} questions, got {$questionCount}");
    }
});

test('new inspection uses active version', function () {
    // Scenario: New inspection uses active version
    // Given an active questionnaire version exists
    $activeVersion = QuestionnaireVersion::getActive();
    expect($activeVersion)->not->toBeNull();
    expect($activeVersion->version_number)->toBe(1);
});

test('new version does not affect existing data', function () {
    // Scenario: New version does not affect existing inspections
    // Given an inspection referencing version 1
    $v1 = QuestionnaireVersion::where('version_number', 1)->first();
    $v1SectionCount = $v1->sections->count();
    $v1QuestionCount = Question::whereIn('category_id',
        Category::whereIn('section_id',
            Section::where('questionnaire_version_id', $v1->id)->pluck('id')
        )->pluck('id')
    )->count();

    // And a new questionnaire version 2 is created
    $v2 = QuestionnaireVersion::create([
        'version_number' => 2,
        'is_active' => true,
    ]);

    // Deactivate v1
    $v1->update(['is_active' => false]);

    // When retrieving the existing version 1
    $v1Fresh = QuestionnaireVersion::find($v1->id);

    // Then it must still have the same data
    expect($v1Fresh->sections->count())->toBe($v1SectionCount);
    expect(Question::whereIn('category_id',
        Category::whereIn('section_id',
            Section::where('questionnaire_version_id', $v1Fresh->id)->pluck('id')
        )->pluck('id')
    )->count())->toBe($v1QuestionCount);

    // And the active version should now be v2
    expect(QuestionnaireVersion::getActive()->version_number)->toBe(2);
});
