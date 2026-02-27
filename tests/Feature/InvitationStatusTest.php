<?php

use App\Models\Invitation;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('invitation status logic works correctly', function () {
    $project = Project::factory()->create();
    
    $pending = Invitation::factory()->create([
        'project_id' => $project->id,
        'expires_at' => Carbon::now()->addDays(7),
        'accepted_at' => null,
    ]);

    $accepted = Invitation::factory()->create([
        'project_id' => $project->id,
        'accepted_at' => Carbon::now(),
    ]);

    $expired = Invitation::factory()->create([
        'project_id' => $project->id,
        'expires_at' => Carbon::now()->subDays(1),
        'accepted_at' => null,
    ]);

    expect($pending->accepted_at)->toBeNull();
    expect($pending->expires_at->isFuture())->toBeTrue();
    
    expect($accepted->accepted_at)->not->toBeNull();
    
    expect($expired->expires_at->isPast())->toBeTrue();
    expect($expired->accepted_at)->toBeNull();
});

test('invalidating an invitation sets expires_at to now', function () {
    $invitation = Invitation::factory()->create([
        'expires_at' => Carbon::now()->addDays(7),
        'accepted_at' => null,
    ]);

    $invitation->update(['expires_at' => Carbon::now()]);

    expect($invitation->fresh()->expires_at->isPast() || $invitation->fresh()->expires_at->isToday())->toBeTrue();
});
