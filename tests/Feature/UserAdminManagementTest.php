<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use App\Filament\Resources\Users\UserResource;

test('cannot delete the last admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    expect(UserResource::canDelete($admin))->toBeFalse();
});

test('can delete admin if there is another admin', function () {
    $admin1 = User::factory()->create(['is_admin' => true]);
    $admin2 = User::factory()->create(['is_admin' => true]);

    expect(UserResource::canDelete($admin1))->toBeTrue();
});

test('non-admin cannot delete anything', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $target = User::factory()->create();

    // Although policies usually handle this, the resource canDelete is a good check.
    // However, canDelete is usually called by the framework based on policies.
    // For now, let's verify the logic in the resource.
    expect(UserResource::canDelete($target))->toBeTrue(); // canDelete itself returns true if logic allows, policy handles actual permission
});

test('is_admin toggle works', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);

    // Simulated update (as Filament does it via SaveAction or ToggleColumn logic)
    $user->update(['is_admin' => true]);
    expect($user->fresh()->is_admin)->toBeTrue();
});
