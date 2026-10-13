<?php

/**
 * Golden rule: evaluators only see the results of their own inspection.
 */

use App\Actions\CloseInspectionAction;
use App\Models\EvaluationRound;
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
    $this->evaluator = User::factory()->create();
    $this->otherEvaluator = User::factory()->create();
    $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);

    foreach ([[$this->owner, 'owner'], [$this->evaluator, 'evaluator'], [$this->otherEvaluator, 'evaluator']] as [$user, $role]) {
        ProjectMember::create(['project_id' => $this->project->id, 'user_id' => $user->id, 'role' => $role]);
    }

    $round = EvaluationRound::factory()->create(['project_id' => $this->project->id, 'status' => 'draft']);

    $this->inspection = Inspection::create([
        'project_id' => $this->project->id,
        'user_id' => $this->otherEvaluator->id,
        'evaluation_round_id' => $round->id,
        'questionnaire_version_id' => QuestionnaireVersion::getActive()->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    Response::create([
        'inspection_id' => $this->inspection->id,
        'question_id' => Question::first()->id,
        'user_id' => $this->otherEvaluator->id,
        'answer' => 'high',
    ]);

    app(CloseInspectionAction::class)->execute($this->inspection);
});

test('evaluator cannot open the results of another evaluator inspection', function () {
    $this->actingAs($this->evaluator)
        ->from(route('projects.show', $this->project))
        ->get(route('results.team', $this->inspection))
        ->assertRedirect(route('projects.show', $this->project))
        ->assertSessionHasErrors('access');
});

test('author and owner can open the inspection results', function () {
    $this->actingAs($this->otherEvaluator)->get(route('results.team', $this->inspection))->assertOk();
    $this->actingAs($this->owner)->get(route('results.team', $this->inspection))->assertOk();
});
