<?php

/**
 * Tests aligned with 009-state_machine.feature and 005-questionnaire_versioning (inspection uses active version)
 */

use App\Models\Inspection;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\QuestionnaireVersion;
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

test('creating inspection uses active questionnaire version', function () {
    // 005: New inspection uses active version
    $response = $this->actingAs($this->owner)
        ->postJson("/projects/{$this->project->id}/inspections");

    $response->assertStatus(201);

    $inspection = Inspection::first();
    $activeVersion = QuestionnaireVersion::getActive();

    expect($inspection->questionnaire_version_id)->toBe($activeVersion->id);
    expect($inspection->status)->toBe('draft');
});

test('draft inspection can transition to active', function () {
    // 009: Valid transitions (draft → active)
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($this->owner)
        ->postJson("/inspections/{$inspection->id}/activate");

    $response->assertStatus(200);
    expect($inspection->fresh()->status)->toBe('active');
    expect($inspection->fresh()->started_at)->not->toBeNull();
});

test('active inspection can transition to closed', function () {
    // 009: Valid transitions (active → closed)
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->actingAs($this->owner)
        ->postJson("/inspections/{$inspection->id}/close");

    $response->assertStatus(200);
    expect($inspection->fresh()->status)->toBe('closed');
    expect($inspection->fresh()->closed_at)->not->toBeNull();
});

test('closed inspection cannot be reopened', function () {
    // 009: Invalid transition (closed → anything)
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'closed',
        'started_at' => now()->subDay(),
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($this->owner)
        ->postJson("/inspections/{$inspection->id}/activate");

    $response->assertStatus(422);
    expect($inspection->fresh()->status)->toBe('closed');
});

test('state machine rejects invalid transitions at model level', function () {
    $inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'draft',
    ]);

    // draft → closed (invalid, must go through active)
    expect(fn () => $inspection->transitionTo('closed'))
        ->toThrow(\InvalidArgumentException::class);

    // active → draft (invalid reverse)
    $inspection->transitionTo('active');
    expect(fn () => $inspection->transitionTo('draft'))
        ->toThrow(\InvalidArgumentException::class);
});
