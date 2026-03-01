<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Inspection;
use App\Models\Question;
use App\Models\Category;
use App\Models\Section;
use App\Models\QuestionnaireVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_export_owned_project_json()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $project->participants()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->get(route('projects.export', $project));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertDownload();
    }

    public function test_user_can_export_member_project_json()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->participants()->attach($owner, ['role' => 'owner']);
        $project->participants()->attach($member, ['role' => 'evaluator']);

        $response = $this->actingAs($member)->get(route('projects.export', $project));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    public function test_user_cannot_export_unauthorized_project_json()
    {
        $owner = User::factory()->create();
        $randomUser = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->participants()->attach($owner, ['role' => 'owner']);

        $response = $this->actingAs($randomUser)->get(route('projects.export', $project));

        $response->assertStatus(403);
    }

    public function test_user_can_export_all_projects_zip()
    {
        $user = User::factory()->create();
        
        $project1 = Project::factory()->create(['owner_id' => $user->id, 'name' => 'Project One']);
        $project1->participants()->attach($user, ['role' => 'owner']);
        
        $owner2 = User::factory()->create();
        $project2 = Project::factory()->create(['owner_id' => $owner2->id, 'name' => 'Project Two']);
        $project2->participants()->attach($owner2, ['role' => 'owner']);
        $project2->participants()->attach($user, ['role' => 'evaluator']);

        $response = $this->actingAs($user)->get(route('profile.export-all'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
        $response->assertDownload();
    }

    public function test_user_can_update_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $project->participants()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->put(route('projects.update', $project), [
            'name' => 'Novo Nome',
            'description' => 'Nova Descrição',
            'url' => 'https://novosite.com',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Novo Nome',
            'description' => 'Nova Descrição',
            'url' => 'https://novosite.com',
        ]);
    }
}
