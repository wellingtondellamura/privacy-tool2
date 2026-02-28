<?php

namespace Database\Factories;

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InspectionPublication>
 */
class InspectionPublicationFactory extends Factory
{
    protected $model = InspectionPublication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inspection_id' => Inspection::factory(),
            'visibility' => Visibility::PRIVATE,
            'published_at' => null,
            'published_by' => null,
        ];
    }

    public function scorePublic(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => Visibility::SCORE_PUBLIC,
            'published_at' => now(),
        ]);
    }

    public function fullPublic(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => Visibility::FULL_PUBLIC,
            'published_at' => now(),
        ]);
    }
}
