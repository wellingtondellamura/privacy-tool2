<?php

/**
 * Tests aligned with 003-invitations.feature
 */

use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);
});

test('owner can invite a new member', function () {
    // Scenario: Invite new member
    // Given an authenticated owner of a project
    // When the owner invites a valid email
    $response = $this->actingAs($this->owner)->post(
        "/projects/{$this->project->id}/invite",
        ['email' => 'newmember@example.com']
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Then a unique invitation token should be generated
    $invitation = Invitation::where('email', 'newmember@example.com')->first();
    expect($invitation)->not->toBeNull();
    expect($invitation->token)->toHaveLength(64);

    // And the invitation should be stored
    $this->assertDatabaseHas('invitations', [
        'project_id' => $this->project->id,
        'email' => 'newmember@example.com',
    ]);
});

test('accept invitation creates new user and adds to project', function () {
    // Scenario: Accept invitation with new account
    // Given a valid invitation exists
    $invitation = Invitation::create([
        'project_id' => $this->project->id,
        'email' => 'newperson@example.com',
        'token' => 'valid-token-12345678901234567890123456789012345678901234567890abcd',
        'role' => 'evaluator',
        'expires_at' => now()->addDays(7),
    ]);

    // And the invited email has no existing account
    expect(User::where('email', 'newperson@example.com')->exists())->toBeFalse();

    // When the invitation token is accepted
    $response = $this->post("/invitations/{$invitation->token}/accept", [
        'name' => 'New Person',
        'password' => 'securepassword',
        'password_confirmation' => 'securepassword',
    ]);

    $response->assertRedirect(route('projects.show', $this->project->id));
    $response->assertSessionHas('success');

    // Then a new user account should be created
    $user = User::where('email', 'newperson@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New Person');

    // And the user should be added to the project
    expect($this->project->hasMember($user))->toBeTrue();
    expect($this->project->getMemberRole($user))->toBe('evaluator');

    // And the invitation should be marked as accepted
    expect($invitation->fresh()->isAccepted())->toBeTrue();
});

test('accept invitation with existing user adds to project', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $invitation = Invitation::create([
        'project_id' => $this->project->id,
        'email' => 'existing@example.com',
        'token' => 'existing-token-234567890123456789012345678901234567890123456789abcd',
        'role' => 'observer',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->post("/invitations/{$invitation->token}/accept");

    $response->assertRedirect(route('projects.show', $this->project->id));
    $response->assertSessionHas('success');
    expect($this->project->hasMember($existingUser))->toBeTrue();
    expect($this->project->getMemberRole($existingUser))->toBe('observer');
});

test('expired invitation returns error', function () {
    // Scenario: Accept expired invitation
    // Given an expired invitation
    $invitation = Invitation::create([
        'project_id' => $this->project->id,
        'email' => 'expired@example.com',
        'token' => 'expired-token-34567890123456789012345678901234567890123456789abcde',
        'role' => 'evaluator',
        'expires_at' => now()->subDay(),
    ]);

    // When the invitation token is used
    $response = $this->post("/invitations/{$invitation->token}/accept", [
        'name' => 'Late Person',
        'password' => 'securepassword',
        'password_confirmation' => 'securepassword',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHasErrors('token');
});

test('non-owner cannot invite members', function () {
    $evaluator = User::factory()->create();
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $evaluator->id,
        'role' => 'evaluator',
    ]);

    $response = $this->actingAs($evaluator)->post(
        "/projects/{$this->project->id}/invite",
        ['email' => 'someone@example.com']
    );

    $response->assertStatus(403);
});
