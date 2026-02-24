<?php

/**
 * Tests aligned with 007-closing.feature
 */

use App\Actions\CloseInspectionAction;
use App\Models\Inspection;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\Response;
use App\Models\ResultSnapshot;
use App\Models\User;
use Database\Seeders\QuestionnaireV1Seeder;

beforeEach(function () {
    $this->seed(QuestionnaireV1Seeder::class);
    $this->owner = User::factory()->create();
    $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);
});

test('close inspection generates individual and consolidated snapshots', function () {
    // Scenario: Close inspection
    $version = QuestionnaireVersion::getActive();
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $evaluator = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $evaluator->id,
        'role' => 'evaluator',
    ]);

    // Submit some responses
    $questions = Question::take(5)->get();
    foreach ($questions as $question) {
        Response::create([
            'inspection_id' => $inspection->id,
            'question_id' => $question->id,
            'user_id' => $evaluator->id,
            'answer' => 'Suficiente',
        ]);
    }

    // Close the inspection
    $action = new CloseInspectionAction();
    $action->execute($inspection);

    // Then individual snapshots must be generated
    $individualSnapshot = ResultSnapshot::where([
        'inspection_id' => $inspection->id,
        'user_id' => $evaluator->id,
    ])->first();
    expect($individualSnapshot)->not->toBeNull();
    expect($individualSnapshot->payload_json)->toHaveKey('sections');

    // And a consolidated snapshot must be generated
    $consolidatedSnapshot = ResultSnapshot::where([
        'inspection_id' => $inspection->id,
        'user_id' => null,
    ])->first();
    expect($consolidatedSnapshot)->not->toBeNull();
    expect($consolidatedSnapshot->payload_json)->toHaveKey('sections');

    // And the inspection status must be set to closed
    expect($inspection->fresh()->status)->toBe('closed');
    expect($inspection->fresh()->closed_at)->not->toBeNull();
});

test('snapshot is immutable after closing', function () {
    // Scenario: Snapshot immutability
    $version = QuestionnaireVersion::getActive();
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $evaluator = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $evaluator->id,
        'role' => 'evaluator',
    ]);

    $question = Question::first();
    Response::create([
        'inspection_id' => $inspection->id,
        'question_id' => $question->id,
        'user_id' => $evaluator->id,
        'answer' => 'Suficiente',
    ]);

    // Close
    $action = new CloseInspectionAction();
    $action->execute($inspection);

    // Store the original snapshot payload
    $snapshot = ResultSnapshot::where([
        'inspection_id' => $inspection->id,
        'user_id' => $evaluator->id,
    ])->first();
    $originalPayload = $snapshot->payload_json;

    // When underlying responses are modified directly in storage
    Response::where([
        'inspection_id' => $inspection->id,
        'question_id' => $question->id,
        'user_id' => $evaluator->id,
    ])->update(['answer' => 'Inexistente']);

    // Then the stored snapshot must remain unchanged
    $snapshotAfter = ResultSnapshot::find($snapshot->id);
    expect($snapshotAfter->payload_json)->toEqual($originalPayload);
});

test('closing via API generates snapshots', function () {
    $version = QuestionnaireVersion::getActive();
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $question = Question::first();
    Response::create([
        'inspection_id' => $inspection->id,
        'question_id' => $question->id,
        'user_id' => $this->owner->id,
        'answer' => 'Suficiente',
    ]);

    $response = $this->actingAs($this->owner)
        ->postJson("/inspections/{$inspection->id}/close");

    $response->assertStatus(200);
    expect(ResultSnapshot::where('inspection_id', $inspection->id)->count())->toBeGreaterThanOrEqual(2);
});
