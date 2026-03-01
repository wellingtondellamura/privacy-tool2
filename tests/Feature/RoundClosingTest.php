<?php

namespace Tests\Feature;

use App\Actions\CloseInspectionAction;
use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\Project;
use App\Models\Response;
use App\Models\User;
use App\Models\QuestionnaireVersion;
use App\Models\Section;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoundClosingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_round_is_closed_when_last_inspection_is_closed()
    {
        // 1. Setup a project and a round with 2 inspections
        $project = Project::factory()->create(['owner_id' => $this->user->id]);
        $round = EvaluationRound::factory()->create(['project_id' => $project->id, 'status' => 'active']);
        
        $version = QuestionnaireVersion::factory()->create();
        $section = Section::factory()->create(['questionnaire_version_id' => $version->id]);
        
        // CloseInspectionAction expects at least 2 categories per section (based on its implementation)
        $c1 = Category::factory()->create(['section_id' => $section->id]);
        $c2 = Category::factory()->create(['section_id' => $section->id]);
        
        $q1 = Question::factory()->create(['category_id' => $c1->id]);
        $q2 = Question::factory()->create(['category_id' => $c2->id]);

        $i1 = Inspection::factory()->active()->create([
            'project_id' => $project->id, 
            'evaluation_round_id' => $round->id,
            'questionnaire_version_id' => $version->id
        ]);
        $i2 = Inspection::factory()->active()->create([
            'project_id' => $project->id, 
            'evaluation_round_id' => $round->id,
            'questionnaire_version_id' => $version->id
        ]);

        // 2. Add responses (Suficiente = 100, Insuficiente = 50)
        // Inspection 1: all Sufficient (100)
        Response::create(['inspection_id' => $i1->id, 'question_id' => $q1->id, 'user_id' => $this->user->id, 'answer' => 'Suficiente']);
        Response::create(['inspection_id' => $i1->id, 'question_id' => $q2->id, 'user_id' => $this->user->id, 'answer' => 'Suficiente']);
        
        // Inspection 2: all Insufficient (50)
        Response::create(['inspection_id' => $i2->id, 'question_id' => $q1->id, 'user_id' => $this->user->id, 'answer' => 'Insuficiente']);
        Response::create(['inspection_id' => $i2->id, 'question_id' => $q2->id, 'user_id' => $this->user->id, 'answer' => 'Insuficiente']);

        $action = app(CloseInspectionAction::class);

        // 3. Close first inspection
        $action->execute($i1);
        
        $round->refresh();
        $this->assertEquals('active', $round->status);

        // 4. Close second inspection (last one)
        $action->execute($i2);

        $round->refresh();
        $this->assertEquals('closed', $round->status);
        $this->assertEquals(1, $round->snapshots()->count());

        $snapshot = $round->snapshots()->first();
        // i1 (100) + i2 (50) -> global avg 75
        $this->assertEquals(75, $snapshot->payload_json['global_score']);
    }
}
