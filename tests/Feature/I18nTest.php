<?php

use App\Models\User;
use App\Models\Project;
use App\Models\EvaluationRound;
use App\Models\RoundBadge;
use App\Models\RoundSnapshot;
use App\Models\RoundPublication;
use App\Enums\Visibility;

// --- SetLocale Middleware ---

test('authenticated user locale is applied from user preference', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    expect(app()->getLocale())->toBe('en');
});

test('authenticated user with pt_BR locale gets pt_BR', function () {
    $user = User::factory()->create(['locale' => 'pt_BR']);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    expect(app()->getLocale())->toBe('pt_BR');
});

test('guest request falls back to Accept-Language header', function () {
    $response = $this->withHeader('Accept-Language', 'en-US,en;q=0.9')
        ->get('/');

    $response->assertOk();
    expect(app()->getLocale())->toBe('en');
});

test('guest request with pt-BR Accept-Language gets pt_BR', function () {
    $response = $this->withHeader('Accept-Language', 'pt-BR,pt;q=0.9')
        ->get('/');

    $response->assertOk();
    expect(app()->getLocale())->toBe('pt_BR');
});

// --- Profile Locale Update ---

test('user can update locale preference', function () {
    $user = User::factory()->create(['locale' => 'pt_BR']);

    $response = $this->actingAs($user)
        ->patch(route('profile.locale'), ['locale' => 'en']);

    $response->assertRedirect();
    expect($user->fresh()->locale)->toBe('en');
});

test('user cannot set invalid locale', function () {
    $user = User::factory()->create(['locale' => 'pt_BR']);

    $response = $this->actingAs($user)
        ->patch(route('profile.locale'), ['locale' => 'fr']);

    $response->assertSessionHasErrors('locale');
    expect($user->fresh()->locale)->toBe('pt_BR');
});

// --- Backend Translation Labels ---

test('medal labels translate correctly in pt_BR', function () {
    app()->setLocale('pt_BR');

    expect(__('labels.medals.gold'))->toBe('Ouro');
    expect(__('labels.medals.silver'))->toBe('Prata');
    expect(__('labels.medals.bronze'))->toBe('Bronze');
    expect(__('labels.medals.incipient'))->toBe('Incipiente');
});

test('medal labels translate correctly in en', function () {
    app()->setLocale('en');

    expect(__('labels.medals.gold'))->toBe('Gold');
    expect(__('labels.medals.silver'))->toBe('Silver');
    expect(__('labels.medals.bronze'))->toBe('Bronze');
    expect(__('labels.medals.incipient'))->toBe('Incipient');
});

test('divergence labels translate correctly in both locales', function () {
    app()->setLocale('pt_BR');
    expect(__('labels.divergence.low'))->toBe('baixa');
    expect(__('labels.divergence.medium'))->toBe('média');
    expect(__('labels.divergence.high'))->toBe('alta');

    app()->setLocale('en');
    expect(__('labels.divergence.low'))->toBe('low');
    expect(__('labels.divergence.medium'))->toBe('medium');
    expect(__('labels.divergence.high'))->toBe('high');
});

test('visibility labels translate correctly in both locales', function () {
    app()->setLocale('pt_BR');
    expect(Visibility::PRIVATE->label())->toBe('Privado');
    expect(Visibility::SCORE_PUBLIC->label())->toBe('Apenas Score');
    expect(Visibility::FULL_PUBLIC->label())->toBe('Relatório Completo');

    app()->setLocale('en');
    expect(Visibility::PRIVATE->label())->toBe('Private');
    expect(Visibility::SCORE_PUBLIC->label())->toBe('Score Only');
    expect(Visibility::FULL_PUBLIC->label())->toBe('Full Report');
});

test('flash messages translate correctly in both locales', function () {
    app()->setLocale('pt_BR');
    expect(__('messages.project_created'))->toBe('Projeto criado com sucesso.');

    app()->setLocale('en');
    expect(__('messages.project_created'))->toBe('Project created successfully.');
});

// --- Services Neutral Keys ---

test('AggregationService medalForScore returns neutral keys', function () {
    expect(\App\Services\AggregationService::medalForScore(95))->toBe('gold');
    expect(\App\Services\AggregationService::medalForScore(75))->toBe('silver');
    expect(\App\Services\AggregationService::medalForScore(50))->toBe('bronze');
    expect(\App\Services\AggregationService::medalForScore(30))->toBe('incipient');
});

test('DivergenceService classify returns neutral keys', function () {
    expect(\App\Services\DivergenceService::classify(5))->toBe('low');
    expect(\App\Services\DivergenceService::classify(20))->toBe('medium');
    expect(\App\Services\DivergenceService::classify(40))->toBe('high');
});

// --- Badge Public Endpoints ---

test('badge public show returns translated medal with lang parameter', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $round = EvaluationRound::factory()->create([
        'project_id' => $project->id,
        'status' => 'closed',
    ]);
    RoundPublication::factory()->create([
        'evaluation_round_id' => $round->id,
        'visibility' => Visibility::SCORE_PUBLIC,
    ]);
    RoundSnapshot::create([
        'evaluation_round_id' => $round->id,
        'payload_json' => [
            'global_score' => 85,
            'medal' => ['name' => 'silver'],
            'sections' => [],
        ],
    ]);
    $badge = RoundBadge::create([
        'evaluation_round_id' => $round->id,
        'public_token' => 'test-token-123',
        'is_active' => true,
        'style' => 'default',
    ]);

    // Portuguese
    $response = $this->getJson('/badge/test-token-123?lang=pt_BR');
    $response->assertOk();
    $response->assertJsonFragment(['medal' => 'Prata']);

    // English
    $response = $this->getJson('/badge/test-token-123?lang=en');
    $response->assertOk();
    $response->assertJsonFragment(['medal' => 'Silver']);
});

test('badge public show falls back to en for invalid lang', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $round = EvaluationRound::factory()->create([
        'project_id' => $project->id,
        'status' => 'closed',
    ]);
    RoundPublication::factory()->create([
        'evaluation_round_id' => $round->id,
        'visibility' => Visibility::SCORE_PUBLIC,
    ]);
    RoundSnapshot::create([
        'evaluation_round_id' => $round->id,
        'payload_json' => [
            'global_score' => 95,
            'medal' => ['name' => 'gold'],
            'sections' => [],
        ],
    ]);
    RoundBadge::create([
        'evaluation_round_id' => $round->id,
        'public_token' => 'test-token-456',
        'is_active' => true,
        'style' => 'default',
    ]);

    $response = $this->getJson('/badge/test-token-456?lang=fr');
    $response->assertOk();
    $response->assertJsonFragment(['medal' => 'Gold']);
});

test('badge script forwards lang parameter to API URL', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $round = EvaluationRound::factory()->create([
        'project_id' => $project->id,
        'status' => 'closed',
    ]);
    RoundPublication::factory()->create([
        'evaluation_round_id' => $round->id,
        'visibility' => Visibility::SCORE_PUBLIC,
    ]);
    RoundSnapshot::create([
        'evaluation_round_id' => $round->id,
        'payload_json' => [
            'global_score' => 85,
            'medal' => ['name' => 'silver'],
            'sections' => [],
        ],
    ]);
    RoundBadge::create([
        'evaluation_round_id' => $round->id,
        'public_token' => 'test-token-789',
        'is_active' => true,
        'style' => 'default',
    ]);

    $response = $this->get(route('badge.script', 'test-token-789') . '?lang=en');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/javascript');
    expect($response->getContent())->toContain('lang=en');
});

// --- Email Translation ---

test('email translation keys exist in both locales', function () {
    app()->setLocale('pt_BR');
    expect(__('email.invitation_subject'))->not->toBe('email.invitation_subject');
    expect(__('email.invitation_heading'))->not->toBe('email.invitation_heading');

    app()->setLocale('en');
    expect(__('email.invitation_subject'))->not->toBe('email.invitation_subject');
    expect(__('email.invitation_heading'))->not->toBe('email.invitation_heading');
});
