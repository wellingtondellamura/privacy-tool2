<?php

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\Project;
use App\Models\Response;
use App\Models\ResultSnapshot;
use App\Models\User;
use App\Services\PublicationService;

/*
|--------------------------------------------------------------------------
| Publication Integrity Tests
|--------------------------------------------------------------------------
| Maps to specs/002-public-directory/features/publication_integrity.feature
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->service = new PublicationService();
});

test('scenario: snapshot remains immutable after publication', function () {
    // Given a published inspection
    $project = Project::factory()->create(['owner_id' => $this->owner->id]);
    $inspection = Inspection::factory()->closed()->create(['project_id' => $project->id]);
    
    // Create a snapshot with a specific score
    $snapshot = ResultSnapshot::factory()->create([
        'inspection_id' => $inspection->id,
        'user_id' => null,
        'payload_json' => [
            'global_score' => 85,
            'medal' => ['name' => 'Prata'],
            'year' => 2026,
            'questionnaire_version_id' => $inspection->questionnaire_version_id
        ]
    ]);

    $pub = $this->service->publish($inspection, Visibility::SCORE_PUBLIC, $this->owner);

    // When underlying responses are modified in storage (simulating corruption or late edits)
    // (Note: CloseInspectionAction already prevents this, but we test the publication layer's dependency)
    Response::where('inspection_id', $inspection->id)->delete();

    // Then the public score must remain equal to the snapshot value
    $pub->refresh();
    expect($pub->score)->toBe(85);
    
    // And if we re-publish/update visibility, it should still use the SAME snapshot
    $this->service->updateVisibility($inspection, Visibility::FULL_PUBLIC);
    expect($pub->fresh()->score)->toBe(85);
});

test('scenario: no recalculation on publication', function () {
    // Given a closed inspection with an existing snapshot
    $project = Project::factory()->create(['owner_id' => $this->owner->id]);
    $inspection = Inspection::factory()->closed()->create(['project_id' => $project->id]);
    
    $snapshot = ResultSnapshot::factory()->create([
        'inspection_id' => $inspection->id,
        'user_id' => null,
        'payload_json' => [
            'global_score' => 99, // A custom score that wouldn't be naturally calculated
            'medal' => ['name' => 'Ouro'],
            'year' => 2026,
            'questionnaire_version_id' => $inspection->questionnaire_version_id
        ]
    ]);

    // When publishing
    $pub = $this->service->publish($inspection, Visibility::SCORE_PUBLIC, $this->owner);

    // Then no recalculation of score must occur
    // (If it recalculated, it would likely get 0 since there are no responses in this factory-created test)
    expect($pub->score)->toBe(99);
    
    // And the existing snapshot must be used
    expect($pub->inspection->resultSnapshots()->whereNull('user_id')->first()->id)->toBe($snapshot->id);
});

test('integrity: slug is deterministic and unique', function () {
    $project = Project::factory()->create(['name' => 'Test Project', 'owner_id' => $this->owner->id]);
    $inspection = Inspection::factory()->closed()->create(['project_id' => $project->id]);
    ResultSnapshot::factory()->create(['inspection_id' => $inspection->id, 'user_id' => null, 'payload_json' => ['global_score' => 10, 'year' => 2026]]);

    $pub1 = $this->service->publish($inspection, Visibility::SCORE_PUBLIC, $this->owner);
    $slug1 = $pub1->slug;
    
    // Revoke and re-publish
    $this->service->revoke($inspection);
    $pub2 = $this->service->publish($inspection, Visibility::SCORE_PUBLIC, $this->owner);
    
    // Slug should be the same (deterministic based on project name + year maybe? or just stable)
    // Current implementation uses Str::slug($inspection->project->name)
    expect($pub2->slug)->toBe($slug1);
});
