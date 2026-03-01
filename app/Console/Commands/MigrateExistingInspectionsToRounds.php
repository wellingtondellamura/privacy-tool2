<?php

namespace App\Console\Commands;

use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateExistingInspectionsToRounds extends Command
{
    protected $signature = 'app:migrate-inspections-to-rounds';
    protected $description = 'Migrate existing inspections to evaluation rounds grouping them by project';

    public function handle()
    {
        $projects = Project::whereHas('inspections', function ($query) {
            $query->whereNull('evaluation_round_id');
        })->get();

        $this->info("Found {$projects->count()} projects with inspections to migrate.");

        foreach ($projects as $project) {
            DB::transaction(function () use ($project) {
                $inspections = $project->inspections()->whereNull('evaluation_round_id')->get();
                
                $round = EvaluationRound::create([
                    'project_id' => $project->id,
                    'name' => 'Rodada Inicial',
                    'status' => 'closed', // Since these are legacy, assuming they belong to a finished cycle
                    'started_at' => $inspections->min('started_at') ?? now(),
                    'closed_at' => $inspections->max('closed_at') ?? now(),
                ]);

                foreach ($inspections as $inspection) {
                    $inspection->update(['evaluation_round_id' => $round->id]);
                }
            });

            $this->info("Project '{$project->name}': Migrated {$project->inspections()->count()} inspections to a new round.");
        }

        $this->info('Migration completed successfully.');
    }
}
