<?php

/**
 * Tests aligned with 008-responses.feature
 */

use App\Models\Inspection;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\Response;
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

    $this->evaluator = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $this->evaluator->id,
        'role' => 'evaluator',
    ]);

    $this->inspection = Inspection::create([
        'project_id' => $this->project->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $this->question = Question::first();
});

test('evaluator can save valid response', function () {
    // Scenario: Save valid response
    $response = $this->actingAs($this->evaluator)
        ->postJson("/inspections/{$this->inspection->id}/response", [
            'question_id' => $this->question->id,
            'answer' => 'Suficiente',
        ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('responses', [
        'inspection_id' => $this->inspection->id,
        'question_id' => $this->question->id,
        'user_id' => $this->evaluator->id,
        'answer' => 'Suficiente',
    ]);
});

test('previous answer is replaced on resubmission', function () {
    // Scenario: any previous answer should be replaced
    Response::create([
        'inspection_id' => $this->inspection->id,
        'question_id' => $this->question->id,
        'user_id' => $this->evaluator->id,
        'answer' => 'Suficiente',
    ]);

    $response = $this->actingAs($this->evaluator)
        ->postJson("/inspections/{$this->inspection->id}/response", [
            'question_id' => $this->question->id,
            'answer' => 'Insuficiente',
        ]);

    $response->assertStatus(201);

    $answers = Response::where([
        'inspection_id' => $this->inspection->id,
        'question_id' => $this->question->id,
        'user_id' => $this->evaluator->id,
    ])->get();

    expect($answers)->toHaveCount(1);
    expect($answers->first()->answer)->toBe('Insuficiente');
});

test('observer cannot respond', function () {
    // Scenario: Observer cannot respond
    $observer = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $observer->id,
        'role' => 'observer',
    ]);

    $response = $this->actingAs($observer)
        ->postJson("/inspections/{$this->inspection->id}/response", [
            'question_id' => $this->question->id,
            'answer' => 'Suficiente',
        ]);

    $response->assertStatus(403);
});

test('cannot respond after closing', function () {
    // Scenario: Cannot respond after closing
    $this->inspection->update([
        'status' => 'closed',
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($this->evaluator)
        ->postJson("/inspections/{$this->inspection->id}/response", [
            'question_id' => $this->question->id,
            'answer' => 'Suficiente',
        ]);

    $response->assertStatus(422);
});
