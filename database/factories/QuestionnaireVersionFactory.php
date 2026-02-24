<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionnaireVersion>
 */
class QuestionnaireVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'version_number' => fake()->unique()->numberBetween(1, 1000),
            'is_active' => true,
        ];
    }
}
