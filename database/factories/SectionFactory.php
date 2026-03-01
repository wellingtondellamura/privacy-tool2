<?php

namespace Database\Factories;

use App\Models\QuestionnaireVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'name' => fake()->words(3, true),
            'order' => fake()->randomDigit(),
        ];
    }
}
