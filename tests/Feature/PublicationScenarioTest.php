<?php

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Publication Feature Tests
|--------------------------------------------------------------------------
| Maps to specs/002-public-directory/features/publication.feature
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);
});

test('scenario: publish closed inspection', function () {
    // Given an inspection with status closed
    $inspection = Inspection::factory()->closed()->create(['project_id' => $this->project->id]);
    
    // And the authenticated user is the project owner
    // When the user publishes the inspection with visibility "score_public"
    $response = $this->actingAs($this->owner)->postJson("/inspections/{$inspection->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then a publication record must be created
    $response->assertStatus(201);
    $this->assertDatabaseHas('inspection_publications', [
        'inspection_id' => $inspection->id,
        'visibility' => 'score_public',
    ]);

    // And published_at must be set
    $publication = InspectionPublication::where('inspection_id', $inspection->id)->first();
    expect($publication->published_at)->not->toBeNull();
    
    // And the inspection must appear in the public directory
    // (This will be truly verified in Phase 5, but we can check if visibility != private)
    expect($publication->visibility)->toBe(Visibility::SCORE_PUBLIC);
});

test('scenario: cannot publish active inspection', function () {
    // Given an inspection with status active
    $inspection = Inspection::factory()->active()->create(['project_id' => $this->project->id]);
    
    // When the owner attempts to publish
    $response = $this->actingAs($this->owner)->postJson("/inspections/{$inspection->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then the system must reject the operation (403 via policy or 422 if handled by validation, spec says 403 usually for forbidden)
    // Actually our policy returns false for non-closed, so it's a 403.
    $response->assertStatus(403);
});

test('scenario: non-owner cannot publish', function () {
    // Given a closed inspection
    $inspection = Inspection::factory()->closed()->create(['project_id' => $this->project->id]);
    
    // And the authenticated user is not owner
    $otherUser = User::factory()->create();
    
    // When attempting to publish
    $response = $this->actingAs($otherUser)->postJson("/inspections/{$inspection->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then the system must return 403 Forbidden
    $response->assertStatus(403);
});

test('scenario: change visibility', function () {
    // Given a published inspection with visibility "score_public"
    $inspection = Inspection::factory()->closed()->create(['project_id' => $this->project->id]);
    InspectionPublication::factory()->scorePublic()->create(['inspection_id' => $inspection->id]);
    
    // When the owner updates visibility to "full_public"
    $response = $this->actingAs($this->owner)->putJson("/inspections/{$inspection->id}/publish", [
        'visibility' => 'full_public',
    ]);

    // Then the publication must reflect the new visibility
    $response->assertStatus(200);
    $this->assertDatabaseHas('inspection_publications', [
        'inspection_id' => $inspection->id,
        'visibility' => 'full_public',
    ]);
});

test('scenario: revoke publication', function () {
    // Given a published inspection
    $inspection = Inspection::factory()->closed()->create(['project_id' => $this->project->id]);
    InspectionPublication::factory()->fullPublic()->create(['inspection_id' => $inspection->id]);
    
    // When the owner revokes publication
    $response = $this->actingAs($this->owner)->deleteJson("/inspections/{$inspection->id}/publish");

    // Then visibility must become "private"
    $response->assertStatus(204);
    $this->assertDatabaseHas('inspection_publications', [
        'inspection_id' => $inspection->id,
        'visibility' => 'private',
    ]);
});
