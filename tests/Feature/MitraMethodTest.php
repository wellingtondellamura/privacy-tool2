<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MitraMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_method_page_is_accessible_by_guests(): void
    {
        $response = $this->get('/metodo-mitra');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('MitraMethod')
        );
    }

    public function test_mitra_method_page_is_accessible_by_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/metodo-mitra');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('MitraMethod')
        );
    }

    public function test_welcome_page_contains_mitra_method_link(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('metodo-mitra');
    }
}
