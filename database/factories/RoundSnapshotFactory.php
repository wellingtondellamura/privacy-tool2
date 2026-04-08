<?php

namespace Database\Factories;

use App\Models\EvaluationRound;
use App\Models\RoundSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoundSnapshotFactory extends Factory
{
    protected $model = RoundSnapshot::class;

    public function definition(): array
    {
        return [
            'evaluation_round_id' => EvaluationRound::factory(),
            'payload_json' => [
                'global_score' => fake()->numberBetween(0, 100),
                'medal' => ['name' => fake()->randomElement(['gold', 'silver', 'bronze', 'incipient'])],
                'sections' => [],
            ],
        ];
    }
}
