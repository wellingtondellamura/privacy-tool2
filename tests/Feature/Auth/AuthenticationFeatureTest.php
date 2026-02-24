<?php

/**
 * Tests aligned with 001-authentication.feature
 */

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('successful user registration creates user with unverified email', function () {
    // Scenario: Successful user registration
    // Given a visitor is not authenticated
    $this->assertGuest();

    // When the visitor submits valid name, email and password
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Then a new user record should be created
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'Test User',
    ]);

    // And the email should be marked as unverified
    $user = User::where('email', 'newuser@example.com')->first();
    expect($user->email_verified_at)->toBeNull();
});

test('registration fires Registered event which triggers verification email', function () {
    // Scenario: Successful user registration — verification email sent
    Notification::fake();

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'verify@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'verify@example.com')->first();

    // And a verification email should be sent
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('successful login with verified email creates session', function () {
    // Scenario: Successful login
    // Given a registered user with verified email exists
    $user = User::factory()->create([
        'email' => 'verified@example.com',
    ]);

    // When the user submits valid credentials
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Then the user should be authenticated
    $this->assertAuthenticated();

    // And a valid session should be created
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('unverified user is redirected to verification notice after login', function () {
    // Edge case: login works but user is sent to verification notice
    $user = User::factory()->unverified()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    // Accessing a verified-only route should redirect to verification notice
    $response = $this->get('/dashboard');
    $response->assertRedirect(route('verification.notice'));
});
