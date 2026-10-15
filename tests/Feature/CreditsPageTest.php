<?php

use Inertia\Testing\AssertableInertia as Assert;

test('credits page lists authors and publications', function () {
    $this->get(route('credits'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Credits')
            ->has('credits.authors', 3)
            ->where('credits.authors.0.name', 'Wellington Aparecido Della Mura')
            ->where('credits.authors.1.name', 'Thiago Adriano Coleti')
            ->where('credits.authors.2.name', 'Marcelo Morandini')
            ->has('credits.publications', 3)
        );
});

test('author names are in the initial html of every page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<meta name="author" content="Wellington Aparecido Della Mura, Thiago Adriano Coleti, Marcelo Morandini">', false);
});
