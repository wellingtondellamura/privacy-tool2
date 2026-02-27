<?php

use App\Models\Inspection;
use App\Models\User;
use App\Models\Project;
use App\Models\QuestionnaireVersion;
use App\Filament\Resources\Inspections\InspectionResource;

test('closed inspection cannot be edited or deleted', function () {
    $inspection = Inspection::factory()->closed()->create();

    expect(InspectionResource::canEdit($inspection))->toBeFalse();
    expect(InspectionResource::canDelete($inspection))->toBeFalse();
});

test('active inspection can be edited', function () {
    $inspection = Inspection::factory()->active()->create();

    expect(InspectionResource::canEdit($inspection))->toBeTrue();
});

test('force close action changes status', function () {
    $inspection = Inspection::factory()->active()->create();
    
    // We simulate the action by calling the transition directly, 
    // as it's what the action does.
    $inspection->transitionTo('closed');
    
    expect($inspection->fresh()->status)->toBe('closed');
    expect($inspection->fresh()->closed_at)->not->toBeNull();
});
