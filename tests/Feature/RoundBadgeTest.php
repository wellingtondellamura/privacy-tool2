<?php

namespace Tests\Feature;

use App\Models\EvaluationRound;
use App\Models\Project;
use App\Models\RoundBadge;
use App\Models\RoundSnapshot;
use App\Models\User;
use App\Models\RoundPublication;
use App\Enums\Visibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoundBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $project;
    protected $round;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->project = Project::factory()->create(['owner_id' => $this->owner->id]);
        
        $this->round = EvaluationRound::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'closed'
        ]);
        
        RoundPublication::factory()->create([
            'evaluation_round_id' => $this->round->id,
            'visibility' => Visibility::SCORE_PUBLIC
        ]);

        RoundSnapshot::create([
            'evaluation_round_id' => $this->round->id,
            'payload_json' => [
                'global_score' => 85,
                'medal' => ['name' => 'Ouro'],
                'sections' => []
            ]
        ]);

        $this->round->refresh();
    }

    public function test_owner_can_create_badge_for_closed_public_round()
    {
        $response = $this->actingAs($this->owner)
            ->post(route('rounds.badge.store', $this->round->id));

        $response->assertStatus(302);
        $this->assertDatabaseHas('round_badges', [
            'evaluation_round_id' => $this->round->id,
            'is_active' => true,
        ]);
    }

    public function test_non_owner_cannot_create_badge()
    {
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($otherUser)
            ->post(route('rounds.badge.store', $this->round->id));

        $response->assertStatus(403);
    }

    public function test_cannot_create_badge_for_private_round()
    {
        $this->round->publicDirectory->update(['visibility' => Visibility::PRIVATE]);

        $response = $this->actingAs($this->owner)
            ->post(route('rounds.badge.store', $this->round->id));

        $this->assertDatabaseMissing('round_badges', ['evaluation_round_id' => $this->round->id]);
    }

    public function test_public_can_access_active_badge_json()
    {
        $badge = RoundBadge::create([
            'evaluation_round_id' => $this->round->id,
            'public_token' => 'secure-token-123',
            'style' => 'default',
            'is_active' => true,
        ]);

        $response = $this->get(route('badge.show', 'secure-token-123'));

        $response->assertStatus(200);
        $response->assertJson([
            'project_name' => $this->project->name,
            'global_score' => 85,
            'medal' => 'Ouro',
        ]);
    }

    public function test_public_cannot_access_revoked_badge()
    {
        $badge = RoundBadge::create([
            'evaluation_round_id' => $this->round->id,
            'public_token' => 'revoked-token',
            'is_active' => false,
        ]);

        $response = $this->get(route('badge.show', 'revoked-token'));

        $response->assertStatus(404);
    }

    public function test_badge_is_invalid_if_round_becomes_private()
    {
        $badge = RoundBadge::create([
            'evaluation_round_id' => $this->round->id,
            'public_token' => 'token-123',
            'is_active' => true,
        ]);

        $this->round->publicDirectory->update(['visibility' => Visibility::PRIVATE]);

        $response = $this->get(route('badge.show', 'token-123'));

        $response->assertStatus(404);
    }
}
