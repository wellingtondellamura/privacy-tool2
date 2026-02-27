<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'email' => $this->faker->safeEmail(),
            'token' => Str::random(32),
            'role' => 'member',
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }
}
