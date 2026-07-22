<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and save active locale preference', function () {
    $response = $this->withSession(['locale' => 'es'])->post('/register', [
        'name' => 'Test User',
        'email' => 'spanish_user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => true,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = \App\Models\User::where('email', 'spanish_user@example.com')->first();
    expect($user->locale)->toBe('es');
});

test('new users can register with explicit locale parameter', function () {
    $response = $this->post('/register', [
        'name' => 'Test User PT',
        'email' => 'pt_user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => true,
        'locale' => 'pt_BR',
    ]);

    $this->assertAuthenticated();

    $user = \App\Models\User::where('email', 'pt_user@example.com')->first();
    expect($user->locale)->toBe('pt_BR');
});
