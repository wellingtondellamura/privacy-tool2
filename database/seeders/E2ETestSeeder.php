<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\QuestionnaireVersion;
use App\Models\ProjectMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2ETestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Questionnaire Version exists and is active
        $activeVersion = QuestionnaireVersion::getActive();
        if (!$activeVersion) {
            $this->call(QuestionnaireV1Seeder::class);
            $activeVersion = QuestionnaireVersion::getActive();
        }

        // Get consensus model from env
        $consensusModel = env('E2E_CONSENSUS_MODEL', 'owner_decides');

        // Clean up any existing E2E Test Project/Users to keep tests clean
        Project::where('name', 'LIKE', 'E2E Test Project%')->forceDelete();
        User::where('email', 'LIKE', 'e2e_%')->delete();

        // 2. Create the 5 users
        $owner = User::create([
            'name' => 'E2E Owner',
            'email' => 'e2e_owner@test.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $evals = [];
        for ($i = 1; $i <= 4; $i++) {
            $evals[] = User::create([
                'name' => "E2E Evaluator $i",
                'email' => "e2e_eval{$i}@test.com",
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
        }

        // 3. Create Project
        $project = Project::create([
            'name' => 'E2E Test Project (' . ucfirst($consensusModel) . ')',
            'description' => 'Projeto de teste E2E automatizado para validar o fluxo.',
            'url' => 'https://mitra-e2e-test.org',
            'owner_id' => $owner->id,
            'icon' => 'shield',
            'color' => 'indigo',
            'require_evidence_for_high' => false,
            'consensus_model' => $consensusModel,
            'is_self_assessment' => false,
            'show_evaluations_to_all' => true,
        ]);

        // 4. Add owner and members as project members
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        foreach ($evals as $eval) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $eval->id,
                'role' => 'evaluator',
            ]);
        }

        // 5. Create Evaluation Round
        $round = EvaluationRound::create([
            'project_id' => $project->id,
            'name' => 'E2E Rodada de Teste E2E',
            'software_version' => '1.0.0-e2e',
            'status' => 'draft',
        ]);

        // 6. Create 5 active inspections (1 for Owner, 4 for Evaluators) and seed responses
        $users = array_merge([$owner], $evals);
        foreach ($users as $userIndex => $user) {
            $inspection = Inspection::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'questionnaire_version_id' => $activeVersion->id,
                'status' => 'active', // active directly so we don't have to click "Ativar"
                'started_at' => now(),
                'evaluation_round_id' => $round->id,
            ]);

            // Seed responses for all questions in this version
            $questionIndex = 0;
            foreach ($activeVersion->sections as $section) {
                foreach ($section->questions as $question) {
                    // Seed answer level
                    $answer = 'high'; // default to high (Suficiente)
                    
                    if ($questionIndex === 0) {
                        // Vary the first question to trigger consensus/divergence!
                        if ($userIndex === 0 || $userIndex === 1) {
                            $answer = 'medium'; // (Insuficiente)
                        } else if ($userIndex === 2 || $userIndex === 3) {
                            $answer = 'low'; // (Inexistente)
                        } else {
                            $answer = 'high'; // (Suficiente)
                        }
                    }

                    \App\Models\Response::create([
                        'inspection_id' => $inspection->id,
                        'question_id' => $question->id,
                        'user_id' => $user->id,
                        'answer' => $answer,
                        'observation' => 'Resposta gerada automaticamente pelo teste E2E.',
                    ]);

                    $questionIndex++;
                }
            }
        }
    }
}
