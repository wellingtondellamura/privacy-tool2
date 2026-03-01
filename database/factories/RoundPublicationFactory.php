<?php

namespace Database\Factories;

use App\Models\EvaluationRound;
use App\Models\RoundPublication;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoundPublicationFactory extends Factory
{
    protected $model = RoundPublication::class;

    public function definition(): array
    {
        return [
            'evaluation_round_id' => EvaluationRound::factory(),
            'visibility' => 'private',
            'score' => fake()->numberBetween(0, 100),
            'medal' => fake()->randomElement(['Ouro', 'Prata', 'Bronze', 'Incipiente']),
            'year' => now()->year,
        ];
    }
}
