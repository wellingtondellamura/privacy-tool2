<?php

namespace Database\Factories;

use App\Models\Inspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResultSnapshot>
 */
class ResultSnapshotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'inspection_id' => Inspection::factory(),
            'user_id' => User::factory(),
            'payload_json' => ['test' => 'data'],
        ];
    }
}
