<?php

use App\Enums\Visibility;
use App\Models\EvaluationRound;
use App\Models\RoundSnapshot;
use App\Models\Project;
use App\Models\Response;
use App\Models\User;
use App\Services\RoundPublicationService;

/*
|--------------------------------------------------------------------------
| Publication Integrity Tests (EvaluationRound)
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->service = app(RoundPublicationService::class);
});

test('scenario: round snapshot remains immutable after publication', function () {
    // Given a published round
    $project = Project::factory()->create(['owner_id' => $this->owner->id, 'name' => 'Integrity Project']);
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $project->id]);
    
    // Create a snapshot with a specific score
    $snapshot = RoundSnapshot::factory()->create([
        'evaluation_round_id' => $round->id,
        'payload_json' => [
            'global_score' => 85,
            'medal' => ['name' => 'Prata'],
            'sections' => []
        ]
    ]);

    $pub = $this->service->publish($round, Visibility::SCORE_PUBLIC, $this->owner);

    // When we attempt to change something indirectly
    // (In round snapshots, there are no individual responses directly linked to the snapshot payload)
    
    // Then the public score must remain equal to the snapshot value
    $pub->refresh();
    expect($pub->score)->toBe(85);
    
    // And if we re-publish/update visibility, it should still use the SAME snapshot
    $this->service->updateVisibility($round, Visibility::FULL_PUBLIC);
    expect($pub->fresh()->score)->toBe(85);
});

test('scenario: no recalculation on round publication', function () {
    // Given a closed round with an existing snapshot
    $project = Project::factory()->create(['owner_id' => $this->owner->id]);
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $project->id]);
    
    $snapshot = RoundSnapshot::factory()->create([
        'evaluation_round_id' => $round->id,
        'payload_json' => [
            'global_score' => 99, 
            'medal' => ['name' => 'Ouro'],
            'sections' => []
        ]
    ]);

    // When publishing
    $pub = $this->service->publish($round, Visibility::SCORE_PUBLIC, $this->owner);

    // Then no recalculation of score must occur
    expect($pub->score)->toBe(99);
    
    // And the existing snapshot must be used
    expect($pub->evaluationRound->snapshots()->latest()->first()->id)->toBe($snapshot->id);
});

test('integrity: round slug is stable', function () {
    $project = Project::factory()->create(['name' => 'Stable Project', 'owner_id' => $this->owner->id]);
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $project->id]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $round->id, 'payload_json' => ['global_score' => 10]]);

    $pub1 = $this->service->publish($round, Visibility::SCORE_PUBLIC, $this->owner);
    $slug1 = $pub1->slug;
    
    // Revoke and re-publish
    $this->service->revoke($round);
    $pub2 = $this->service->publish($round, Visibility::SCORE_PUBLIC, $this->owner);
    
    // Slug should be the same
    expect($pub2->slug)->toBe($slug1);
});
