<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

test('non-admin user cannot access admin panel', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user)
        ->get('/admin')
        ->assertStatus(403);
});

test('admin user can access admin panel', function () {
    $user = User::factory()->create(['is_admin' => true]);

    actingAs($user)
        ->get('/admin')
        ->assertStatus(200);
});

test('unauthenticated user is redirected to login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});
