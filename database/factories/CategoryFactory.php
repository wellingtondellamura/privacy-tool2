<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'name' => fake()->words(3, true),
            'order' => fake()->randomDigit(),
        ];
    }
}
