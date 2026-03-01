<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\ProjectMember;
use App\Models\QuestionnaireVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;

class DataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_transform_project_data_includes_rounds_and_inspections()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        
        $round = EvaluationRound::factory()->create([
            'project_id' => $project->id,
            'name' => 'Round 1',
        ]);

        $qv = QuestionnaireVersion::factory()->create();

        $inspection = Inspection::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'evaluation_round_id' => $round->id,
            'questionnaire_version_id' => $qv->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $controller = new \App\Http\Controllers\DataExportController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('transformProjectData');
        $method->setAccessible(true);
        
        $data = $method->invoke($controller, $project);

        $this->assertArrayHasKey('rounds', $data);
        $this->assertCount(1, $data['rounds']);
        $this->assertEquals('Round 1', $data['rounds'][0]['name']);
        $this->assertCount(1, $data['rounds'][0]['inspections']);
        $this->assertEquals($inspection->id, $data['rounds'][0]['inspections'][0]['id']);
    }
}
