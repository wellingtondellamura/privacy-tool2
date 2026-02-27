<?php

use App\Models\QuestionnaireVersion;
use App\Models\Inspection;
use App\Models\Question;
use App\Models\Category;
use App\Models\Section;
use App\Filament\Resources\QuestionnaireVersions\QuestionnaireVersionResource;
use App\Filament\Resources\Questions\QuestionResource;

test('only one questionnaire version can be active', function () {
    $v1 = QuestionnaireVersion::factory()->create(['is_active' => true]);
    $v2 = QuestionnaireVersion::factory()->create(['is_active' => true]);

    expect($v1->fresh()->is_active)->toBeFalse();
    expect($v2->fresh()->is_active)->toBeTrue();
});

test('cannot edit or delete questionnaire version with inspections', function () {
    $version = QuestionnaireVersion::factory()->create();
    Inspection::factory()->create(['questionnaire_version_id' => $version->id]);

    expect(QuestionnaireVersionResource::canEdit($version))->toBeFalse();
    expect(QuestionnaireVersionResource::canDelete($version))->toBeFalse();
});

test('cannot edit or delete question if version has inspections', function () {
    $version = QuestionnaireVersion::factory()->create();
    $section = Section::factory()->create(['questionnaire_version_id' => $version->id]);
    $category = Category::factory()->create(['section_id' => $section->id]);
    $question = Question::factory()->create(['category_id' => $category->id]);

    Inspection::factory()->create(['questionnaire_version_id' => $version->id]);

    expect(QuestionResource::canEdit($question))->toBeFalse();
    expect(QuestionResource::canDelete($question))->toBeFalse();
});
