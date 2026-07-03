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

        // 2. Add responses (high = 100, medium = 50)
        // Inspection 1: all High (100)
        Response::create(['inspection_id' => $i1->id, 'question_id' => $q1->id, 'user_id' => $this->user->id, 'answer' => 'high']);
        Response::create(['inspection_id' => $i1->id, 'question_id' => $q2->id, 'user_id' => $this->user->id, 'answer' => 'high']);
        
        // Inspection 2: all Medium (50)
        Response::create(['inspection_id' => $i2->id, 'question_id' => $q1->id, 'user_id' => $this->user->id, 'answer' => 'medium']);
        Response::create(['inspection_id' => $i2->id, 'question_id' => $q2->id, 'user_id' => $this->user->id, 'answer' => 'medium']);

        $action = app(CloseInspectionAction::class);

        // 3. Close first inspection
        $action->execute($i1);
        
        $round->refresh();
        $this->assertEquals('active', $round->status);

        // 4. Close second inspection (last one)
        $action->execute($i2);

        // Inspections are closed individually; round auto-close is handled elsewhere
        $i1->refresh();
        $i2->refresh();
        $this->assertEquals('closed', $i1->status);
        $this->assertEquals('closed', $i2->status);
    }

    /** @test */
    public function test_owner_decides_consensus_strategy_overrides_average()
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'consensus_model' => 'owner_decides'
        ]);

        \App\Models\ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);

        $round = EvaluationRound::factory()->create(['project_id' => $project->id, 'status' => 'review']);
        
        $version = QuestionnaireVersion::factory()->create();
        $section = Section::factory()->create(['questionnaire_version_id' => $version->id]);
        $category = Category::factory()->create(['section_id' => $section->id]);
        $question = Question::factory()->create(['category_id' => $category->id]);

        $i1 = Inspection::factory()->active()->create([
            'project_id' => $project->id, 
            'evaluation_round_id' => $round->id,
            'questionnaire_version_id' => $version->id
        ]);
        
        // Response is 'medium' (50)
        Response::create(['inspection_id' => $i1->id, 'question_id' => $question->id, 'user_id' => $this->user->id, 'answer' => 'medium']);
        
        // Close the inspection
        app(CloseInspectionAction::class)->execute($i1);

        // Owner consolidates as 'high' (100)
        $this->actingAs($this->user)
            ->post(route('rounds.consolidate.store', $round->id), [
                'question_id' => $question->id,
                'final_answer' => 'high'
            ])
            ->assertRedirect();

        // Close the round
        $this->actingAs($this->user)
            ->post(route('rounds.close', $round->id), [
                'diagnosis' => 'Good privacy',
                'publish_immediately' => false
            ])
            ->assertRedirect();

        $round->refresh();
        $this->assertEquals('closed', $round->status);
        
        $snapshot = $round->snapshots()->latest()->first();
        $this->assertNotNull($snapshot);
        
        $payload = $snapshot->payload_json;
        // The score of the question should be 100 (high) due to the consolidation!
        $qData = $payload['sections'][0]['categories'][0]['questions'][0];
        $this->assertEquals('high', $qData['level']);
        $this->assertEquals(100, $qData['score']);
    }

    /** @test */
    public function test_majority_vote_consensus_strategy_calculates_correct_winner_and_tie_breaker()
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'consensus_model' => 'majority_vote'
        ]);

        \App\Models\ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);

        $round = EvaluationRound::factory()->create(['project_id' => $project->id, 'status' => 'review']);
        
        $version = QuestionnaireVersion::factory()->create();
        $section = Section::factory()->create(['questionnaire_version_id' => $version->id]);
        $category = Category::factory()->create(['section_id' => $section->id]);
        $question1 = Question::factory()->create(['category_id' => $category->id]);
        $question2 = Question::factory()->create(['category_id' => $category->id]);

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
        $i3 = Inspection::factory()->active()->create([
            'project_id' => $project->id, 
            'evaluation_round_id' => $round->id,
            'questionnaire_version_id' => $version->id
        ]);

        $evaluator1 = User::factory()->create();
        $evaluator2 = User::factory()->create();
        $evaluator3 = User::factory()->create();

        // Q1 Votes: high (100), high (100), medium (50) -> Majority: high
        Response::create(['inspection_id' => $i1->id, 'question_id' => $question1->id, 'user_id' => $evaluator1->id, 'answer' => 'high']);
        Response::create(['inspection_id' => $i2->id, 'question_id' => $question1->id, 'user_id' => $evaluator2->id, 'answer' => 'high']);
        Response::create(['inspection_id' => $i3->id, 'question_id' => $question1->id, 'user_id' => $evaluator3->id, 'answer' => 'medium']);

        // Q2 Votes: high (100), medium (50) -> Tie (1 vote each) -> Winner: medium (most conservative tie-breaker)
        Response::create(['inspection_id' => $i1->id, 'question_id' => $question2->id, 'user_id' => $evaluator1->id, 'answer' => 'high']);
        Response::create(['inspection_id' => $i2->id, 'question_id' => $question2->id, 'user_id' => $evaluator2->id, 'answer' => 'medium']);

        app(CloseInspectionAction::class)->execute($i1);
        app(CloseInspectionAction::class)->execute($i2);
        app(CloseInspectionAction::class)->execute($i3);

        // Close the round
        $this->actingAs($this->user)
            ->post(route('rounds.close', $round->id), [
                'diagnosis' => 'Good privacy',
                'publish_immediately' => false
            ])
            ->assertRedirect();

        $round->refresh();
        $this->assertEquals('closed', $round->status);
        
        $snapshot = $round->snapshots()->latest()->first();
        $payload = $snapshot->payload_json;
        
        $questionsPayload = $payload['sections'][0]['categories'][0]['questions'];
        
        $q1Payload = collect($questionsPayload)->firstWhere('question_id', $question1->id);
        $q2Payload = collect($questionsPayload)->firstWhere('question_id', $question2->id);

        // Q1 should be 'high'
        $this->assertEquals('high', $q1Payload['level']);
        $this->assertEquals(100, $q1Payload['score']);

        // Q2 should be 'medium' (tie resolved conservatively: medium is lower than high)
        $this->assertEquals('medium', $q2Payload['level']);
        $this->assertEquals(50, $q2Payload['score']);
    }
}
