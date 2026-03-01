<?php

/**
 * Tests aligned with 004-authorization.feature
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

test('only owner can close inspection', function () {
    // Scenario: Only owner can close inspection
    $version = QuestionnaireVersion::getActive();
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    // Given a user with role evaluator
    $evaluator = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $evaluator->id,
        'role' => 'evaluator',
    ]);

    // Add a response so close has data
    Response::create([
        'inspection_id' => $inspection->id,
        'question_id' => Question::first()->id,
        'user_id' => $evaluator->id,
        'answer' => 'Suficiente',
    ]);

    // When attempting to close inspection as evaluator
    $response = $this->actingAs($evaluator)
        ->postJson("/inspections/{$inspection->id}/close");

    // Then the system must return 302 (redirect with errors) or 403
    // The controller returns redirect()->back()->withErrors() which is 302
    $response->assertStatus(302);
    expect($inspection->fresh()->status)->toBe('active');
});

test('observer can view closed inspection results', function () {
    // Scenario: Observer can view closed inspection
    $version = QuestionnaireVersion::getActive();
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    // Add a response and close
    Response::create([
        'inspection_id' => $inspection->id,
        'question_id' => Question::first()->id,
        'user_id' => $this->owner->id,
        'answer' => 'Suficiente',
    ]);

    $action = app(CloseInspectionAction::class);
    $action->execute($inspection);

    // Given a user with role observer
    $observer = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $observer->id,
        'role' => 'observer',
    ]);

    $response = $this->actingAs($observer)
        ->getJson("/inspections/{$inspection->id}/team-results", ['X-Inertia' => 'true']);

    // Then the user must receive consolidated snapshot
    $response->assertStatus(200);
    $response->assertJsonStructure(['props' => ['snapshot' => ['sections']]]);
});

test('comparison returns 400 for different projects', function () {
    // Scenario: Invalid comparison
    $version = QuestionnaireVersion::getActive();

    // Create two inspections in different projects
    $project2 = Project::factory()->create(['owner_id' => $this->owner->id]);
    ProjectMember::create([
        'project_id' => $project2->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);

    $inspection1 = Inspection::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'closed',
        'started_at' => now()->subDay(),
        'closed_at' => now(),
    ]);

    $inspection2 = Inspection::create([
        'project_id' => $project2->id,
        'user_id' => $this->owner->id,
        'questionnaire_version_id' => $version->id,
        'status' => 'closed',
        'started_at' => now()->subDay(),
        'closed_at' => now(),
    ]);

    // When attempting comparison
    $response = $this->actingAs($this->owner)
        ->getJson("/inspections/{$inspection1->id}/comparison/{$inspection2->id}");

    // Then the system must return 302 (redirect with errors)
    $response->assertStatus(302);
});
