<?php

use App\Enums\Visibility;
use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\RoundPublication;
use App\Models\RoundSnapshot;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ResultSnapshot;
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

test('scenario: publish closed round', function () {
    // Given an evaluation round with status closed
    $round = EvaluationRound::factory()->closed()->create([
        'project_id' => $this->project->id,
    ]);
    RoundSnapshot::factory()->create([
        'evaluation_round_id' => $round->id,
        'payload_json' => ['global_score' => 85, 'medal' => ['name' => 'Prata']],
    ]);
    
    // And the authenticated user is the project owner
    // When the user publishes the round with visibility "score_public"
    $response = $this->actingAs($this->owner)->postJson("/rounds/{$round->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then a publication record must be created
    $response->assertStatus(302);
    $this->assertDatabaseHas('round_publications', [
        'evaluation_round_id' => $round->id,
        'visibility' => 'score_public',
    ]);

    // And published_at must be set
    $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();
    expect($publication->published_at)->not->toBeNull();
    
    expect($publication->visibility)->toBe(Visibility::SCORE_PUBLIC);
});

test('scenario: cannot publish active round', function () {
    // Given an evaluation round with status active
    $round = EvaluationRound::factory()->create(['project_id' => $this->project->id]);
    
    // When the owner attempts to publish
    $response = $this->actingAs($this->owner)->postJson("/rounds/{$round->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then the system must reject the operation (403)
    $response->assertStatus(403);
});

test('scenario: non-owner cannot publish', function () {
    // Given a closed round
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $this->project->id]);
    
    // And the authenticated user is not owner
    $otherUser = User::factory()->create();
    
    // When attempting to publish
    $response = $this->actingAs($otherUser)->postJson("/rounds/{$round->id}/publish", [
        'visibility' => 'score_public',
    ]);

    // Then the system must return 403 Forbidden
    $response->assertStatus(403);
});

test('scenario: change visibility', function () {
    // Given a published round with visibility "score_public"
    $round = EvaluationRound::factory()->closed()->create([
        'project_id' => $this->project->id,
    ]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $round->id]);
    RoundPublication::factory()->create([
        'evaluation_round_id' => $round->id,
        'visibility' => 'score_public',
    ]);
    
    // When the owner updates visibility to "full_public"
    $response = $this->actingAs($this->owner)->putJson("/rounds/{$round->id}/publish", [
        'visibility' => 'full_public',
    ]);

    // Then the publication must reflect the new visibility
    $response->assertStatus(302);
    $this->assertDatabaseHas('round_publications', [
        'evaluation_round_id' => $round->id,
        'visibility' => 'full_public',
    ]);
});

test('scenario: revoke publication', function () {
    // Given a published round
    $round = EvaluationRound::factory()->closed()->create([
        'project_id' => $this->project->id,
    ]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $round->id]);
    RoundPublication::factory()->create([
        'evaluation_round_id' => $round->id,
        'visibility' => 'full_public',
    ]);
    
    // When the owner revokes publication
    $response = $this->actingAs($this->owner)->deleteJson("/rounds/{$round->id}/publish");

    // Then visibility must become "private"
    $response->assertStatus(302);
    $this->assertDatabaseHas('round_publications', [
        'evaluation_round_id' => $round->id,
        'visibility' => 'private',
    ]);
});
