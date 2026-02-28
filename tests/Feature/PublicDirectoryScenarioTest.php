<?php

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\Project;
use App\Models\ResultSnapshot;
use App\Models\User;
use App\Services\PublicationService;

/*
|--------------------------------------------------------------------------
| Public Directory Feature Tests
|--------------------------------------------------------------------------
| Maps to specs/002-public-directory/features/public_directory.feature
*/

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->service = new PublicationService();
});

test('scenario: list public tools', function () {
    // Given multiple inspections with mixed visibility
    $p1 = Project::factory()->create(['name' => 'Tool A']);
    $i1 = Inspection::factory()->closed()->create(['project_id' => $p1->id]);
    ResultSnapshot::factory()->create(['inspection_id' => $i1->id, 'user_id' => null, 'payload_json' => ['global_score' => 95, 'medal' => ['name' => 'Ouro']]]);
    $this->service->publish($i1, Visibility::SCORE_PUBLIC, $this->owner);

    $p2 = Project::factory()->create(['name' => 'Tool B']);
    $i2 = Inspection::factory()->closed()->create(['project_id' => $p2->id]);
    ResultSnapshot::factory()->create(['inspection_id' => $i2->id, 'user_id' => null, 'payload_json' => ['global_score' => 80, 'medal' => ['name' => 'Prata']]]);
    $this->service->publish($i2, Visibility::FULL_PUBLIC, $this->owner);

    $p3 = Project::factory()->create(['name' => 'Tool C']);
    $i3 = Inspection::factory()->closed()->create(['project_id' => $p3->id]);
    ResultSnapshot::factory()->create(['inspection_id' => $i3->id, 'user_id' => null]);
    $pub3 = $this->service->publish($i3, Visibility::PRIVATE, $this->owner);

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
    $inspection = Inspection::factory()->closed()->create(['project_id' => $project->id]);
    ResultSnapshot::factory()->create([
        'inspection_id' => $inspection->id, 
        'user_id' => null, 
        'payload_json' => [
            'global_score' => 88,
            'medal' => ['name' => 'Prata'],
            'sections' => [
                ['name' => 'Sec1', 'score' => 90, 'medal' => 'Ouro']
            ],
            'user_count' => 5
        ]
    ]);
    $pub = $this->service->publish($inspection, Visibility::SCORE_PUBLIC, $this->owner);

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
    $inspection = Inspection::factory()->closed()->create(['project_id' => $project->id]);
    ResultSnapshot::factory()->create([
        'inspection_id' => $inspection->id, 
        'user_id' => null, 
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
    $pub = $this->service->publish($inspection, Visibility::FULL_PUBLIC, $this->owner);

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
    $inspection = Inspection::factory()->closed()->create();
    ResultSnapshot::factory()->create(['inspection_id' => $inspection->id, 'user_id' => null, 'payload_json' => ['global_score' => 50, 'medal' => ['name' => 'Bronze']]]);
    $pub = $this->service->publish($inspection, Visibility::PRIVATE, $this->owner);

    // When accessing its public page
    $response = $this->getJson("/tools/{$pub->slug}", ['X-Inertia' => 'true']);

    // Then the system must return 404
    $response->assertStatus(404);
});
