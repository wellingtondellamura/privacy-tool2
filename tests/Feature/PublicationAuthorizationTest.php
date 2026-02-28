<?php

use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Publication Authorization Tests
|--------------------------------------------------------------------------
| Validates Phase 2 - policy and authorization rules
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);
    ProjectMember::create([
        'project_id' => $this->project->id,
        'user_id' => $this->owner->id,
        'role' => 'owner',
    ]);

    $this->otherUser = User::factory()->create();
    
    $this->closedInspection = Inspection::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'closed',
    ]);

    $this->activeInspection = Inspection::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'active',
    ]);
});

test('owner can publish a closed inspection', function () {
    expect(Gate::forUser($this->owner)->allows('create', [InspectionPublication::class, $this->closedInspection]))
        ->toBeTrue();
});

test('owner cannot publish an active inspection', function () {
    // RN-PUB-01: status = closed
    expect(Gate::forUser($this->owner)->allows('create', [InspectionPublication::class, $this->activeInspection]))
        ->toBeFalse();
});

test('non-owner cannot publish even if closed', function () {
    // RN-PUB-02: role = owner
    expect(Gate::forUser($this->otherUser)->allows('create', [InspectionPublication::class, $this->closedInspection]))
        ->toBeFalse();
});

test('owner can manage their own publication', function () {
    $publication = InspectionPublication::factory()->create([
        'inspection_id' => $this->closedInspection->id,
    ]);

    expect(Gate::forUser($this->owner)->allows('view', $publication))->toBeTrue();
    expect(Gate::forUser($this->owner)->allows('update', $publication))->toBeTrue();
    expect(Gate::forUser($this->owner)->allows('delete', $publication))->toBeTrue();
});

test('other users cannot manage publication', function () {
    $publication = InspectionPublication::factory()->create([
        'inspection_id' => $this->closedInspection->id,
    ]);

    expect(Gate::forUser($this->otherUser)->allows('view', $publication))->toBeFalse();
    expect(Gate::forUser($this->otherUser)->allows('update', $publication))->toBeFalse();
    expect(Gate::forUser($this->otherUser)->allows('delete', $publication))->toBeFalse();
});
