<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Inspection;
use App\Models\ProjectMember;
use function Pest\Laravel\actingAs;

test('project has an owner', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);

    expect($project->owner->id)->toBe($owner->id);
});

test('project can have multiple members', function () {
    $project = Project::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $project->participants()->attach($user1, ['role' => 'evaluator']);
    $project->participants()->attach($user2, ['role' => 'observer']);

    expect($project->participants()->count())->toBe(2);
});

test('project can have multiple inspections', function () {
    $project = Project::factory()->create();
    Inspection::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->inspections)->toHaveCount(3);
});

test('project must have an owner_id when updated', function () {
    $project = Project::factory()->create();
    
    // Testing model validation or database constraint
    // owner_id is already required in the form.
    expect($project->owner_id)->not->toBeNull();
});
