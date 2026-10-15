<?php

use Inertia\Testing\AssertableInertia as Assert;

test('terms of use page receives legal configuration', function () {
    config(['legal.controller_name' => 'Controlador Teste', 'legal.contact_email' => 'contato@example.com']);

    $this->get(route('terms.use'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('TermsOfUse')
            ->where('legal.controller_name', 'Controlador Teste')
            ->where('legal.contact_email', 'contato@example.com')
            ->where('legal.version', config('legal.version'))
        );
});

test('privacy policy page receives legal configuration', function () {
    config(['legal.dpo_name' => 'Encarregado Teste']);

    $this->get(route('privacy.policy'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PrivacyPolicy')
            ->where('legal.dpo_name', 'Encarregado Teste')
            ->has('legal.updated_at')
        );
});
