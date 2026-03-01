<?php

use App\Enums\Visibility;
use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\RoundPublication;
use App\Models\RoundSnapshot;
use App\Models\Project;
use App\Models\ResultSnapshot;
use App\Models\User;
use App\Services\RoundPublicationService;

/*
|--------------------------------------------------------------------------
| Public Directory Feature Tests
|--------------------------------------------------------------------------
| Maps to specs/002-public-directory/features/public_directory.feature
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->service = new RoundPublicationService();
});

test('scenario: list public tools', function () {
    // Given multiple inspections with mixed visibility
    $p1 = Project::factory()->create(['name' => 'Tool A']);
    $r1 = EvaluationRound::factory()->closed()->create(['project_id' => $p1->id]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $r1->id, 'payload_json' => ['global_score' => 95, 'medal' => ['name' => 'Ouro']]]);
    $this->service->publish($r1, Visibility::SCORE_PUBLIC, $this->owner);

    $p2 = Project::factory()->create(['name' => 'Tool B']);
    $r2 = EvaluationRound::factory()->closed()->create(['project_id' => $p2->id]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $r2->id, 'payload_json' => ['global_score' => 80, 'medal' => ['name' => 'Prata']]]);
    $this->service->publish($r2, Visibility::FULL_PUBLIC, $this->owner);

    $p3 = Project::factory()->create(['name' => 'Tool C']);
    $r3 = EvaluationRound::factory()->closed()->create(['project_id' => $p3->id]);
    RoundSnapshot::factory()->create(['evaluation_round_id' => $r3->id]);
    $this->service->publish($r3, Visibility::PRIVATE, $this->owner);

    // When accessing /tools
    $response = $this->getJson('/tools', ['X-Inertia' => 'true']);

    // Then only inspections with visibility not equal to "private" must be listed
    $response->assertStatus(200);
    $data = $response->json('props.tools.data');
    
    expect(collect($data)->pluck('project_name'))->toContain('Tool A', 'Tool B');
    expect(collect($data)->pluck('project_name'))->not->toContain('Tool C');
});

test('scenario: access score_public inspection', function () {
    // Given an inspection with visibility "score_public"
    $project = Project::factory()->create(['name' => 'EcoTool']);
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $project->id]);
    RoundSnapshot::factory()->create([
        'evaluation_round_id' => $round->id, 
        'payload_json' => [
            'global_score' => 88,
            'medal' => ['name' => 'Prata'],
            'sections' => [
                ['name' => 'Sec1', 'score' => 90, 'medal' => 'Ouro']
            ],
            'user_count' => 5
        ]
    ]);
    $pub = $this->service->publish($round, Visibility::SCORE_PUBLIC, $this->owner);

    // When accessing its public page
    $response = $this->getJson("/tools/{$pub->slug}", ['X-Inertia' => 'true']);

    // Then only summary data must be visible
    $response->assertStatus(200);
    $tool = $response->json('props.tool');
    
    expect($tool['name'])->toBe('EcoTool');
    expect($tool['score'])->toBe(88);
    expect($tool['medal']['name'])->toBe('Prata');
    
    // And detailed report must not be visible
    expect($tool)->not->toHaveKey('report');
});

test('scenario: access full_public inspection', function () {
    // Given an inspection with visibility "full_public"
    $project = Project::factory()->create(['name' => 'FullTool']);
    $round = EvaluationRound::factory()->closed()->create(['project_id' => $project->id]);
    RoundSnapshot::factory()->create([
        'evaluation_round_id' => $round->id, 
        'payload_json' => [
            'global_score' => 92,
            'medal' => ['name' => 'Ouro'],
            'sections' => [
                [
                    'name' => 'Privacy', 
                    'score' => 95, 
                    'medal' => 'Ouro',
                    'categories' => [
                        [
                            'name' => 'Cat1',
                            'questions' => [
                                ['question_id' => 1, 'question_text' => 'Q1', 'score' => 100, 'user_id' => 999]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);
    $pub = $this->service->publish($round, Visibility::FULL_PUBLIC, $this->owner);

    // When accessing its public page
    $response = $this->getJson("/tools/{$pub->slug}", ['X-Inertia' => 'true']);

    // Then the complete consolidated report must be visible
    $response->assertStatus(200);
    $tool = $response->json('props.tool');
    expect($tool)->toHaveKey('report');
    
    // And no individual responses must be visible
    $report = json_encode($tool['report']);
    expect($report)->not->toContain('"user_id":999');
});

test('scenario: access private inspection', function () {
    // Given an inspection with visibility "private"
    $round = EvaluationRound::factory()->closed()->create();
    RoundSnapshot::factory()->create(['evaluation_round_id' => $round->id, 'payload_json' => ['global_score' => 50, 'medal' => ['name' => 'Bronze']]]);
    $pub = $this->service->publish($round, Visibility::PRIVATE, $this->owner);

    // When accessing its public page
    $response = $this->getJson("/tools/{$pub->slug}", ['X-Inertia' => 'true']);

    // Then the system must return 404
    $response->assertStatus(404);
});
