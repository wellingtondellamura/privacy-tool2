<?php

namespace Database\Factories;

use App\Models\EvaluationRound;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationRoundFactory extends Factory
{
    protected $model = EvaluationRound::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(3, true),
            'status' => 'active',
            'started_at' => now(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }
}
