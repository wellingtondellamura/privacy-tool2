<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\QuestionnaireVersion;
use App\Models\User;
use App\Models\EvaluationRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inspection>
 */
class InspectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'evaluation_round_id' => EvaluationRound::factory(),
            'status' => 'draft',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
            'started_at' => now()->subDay(),
            'closed_at' => now(),
        ]);
    }
}
