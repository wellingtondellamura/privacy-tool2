<?php

/**
 * Tests aligned with 002-projects.feature
 */

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

test('authenticated user can create a project', function () {
    // Scenario: Create project
    // Given an authenticated user
    $user = User::factory()->create();

    // When the user creates a project with valid name and URL
    $response = $this->actingAs($user)->postJson('/projects', [
        'name' => 'My Privacy Project',
        'url' => 'https://example.com',
    ]);

    // Then the project should be persisted
    $response->assertStatus(201);
    $this->assertDatabaseHas('projects', [
        'name' => 'My Privacy Project',
        'url' => 'https://example.com',
        'owner_id' => $user->id,
    ]);

    // And the user should be assigned as owner
    $project = Project::where('name', 'My Privacy Project')->first();
    expect($project->getMemberRole($user))->toBe('owner');

    // And the project should have exactly one owner
    $ownerCount = ProjectMember::where('project_id', $project->id)
        ->where('role', 'owner')
        ->count();
    expect($ownerCount)->toBe(1);
});

test('non-member cannot access project', function () {
    // Scenario: Unauthorized access to project
    // Given a user who is not a member of a project
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    ProjectMember::create([
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => 'owner',
    ]);

    $outsider = User::factory()->create();

    // When the user attempts to access the project
    $response = $this->actingAs($outsider)->getJson("/projects/{$project->id}");

    // Then the system should return 403 Forbidden
    $response->assertStatus(403);
});

test('member can access project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    ProjectMember::create([
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($owner)->getJson("/projects/{$project->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $project->name]);
});

test('owner can update project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    ProjectMember::create([
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($owner)->putJson("/projects/{$project->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(200);
    expect($project->fresh()->name)->toBe('Updated Name');
});

test('owner can delete project', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    ProjectMember::create([
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($owner)->deleteJson("/projects/{$project->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

test('unauthenticated user cannot access projects', function () {
    $response = $this->getJson('/projects');

    $response->assertStatus(401);
});
