<?php

namespace App\Console\Commands;

use App\Models\EvaluationRound;
use App\Models\InspectionPublication;
use App\Models\RoundPublication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePublicationsToRounds extends Command
{
    protected $signature = 'app:migrate-publications-to-rounds';
    protected $description = 'Migrate existing inspection publications to round publications';

    public function handle()
    {
        $publications = InspectionPublication::all();

        $this->info("Found {$publications->count()} inspection publications to migrate.");

        foreach ($publications as $oldPub) {
            $inspection = $oldPub->inspection;
            if (!$inspection || !$inspection->evaluation_round_id) {
                $this->warn("Skipping publication {$oldPub->id}: Inspection not found or not linked to a round.");
                continue;
            }

            $round = $inspection->evaluationRound;

            DB::transaction(function () use ($oldPub, $round) {
                // Check if the round already has a publication
                if (RoundPublication::where('evaluation_round_id', $round->id)->exists()) {
                    return;
                }

                RoundPublication::create([
                    'evaluation_round_id' => $round->id,
                    'visibility' => $oldPub->visibility,
                    'slug' => $oldPub->slug, // Reuse slug if possible, might need conflict handling
                    'published_at' => $oldPub->published_at,
                    'published_by' => $oldPub->published_by,
                    'score' => $oldPub->score,
                    'medal' => $oldPub->medal,
                    'year' => $oldPub->year,
                    'questionnaire_version_id' => $oldPub->questionnaire_version_id,
                ]);
            });

            $this->info("Migrated publication for round '{$round->name}' (from inspection {$inspection->id}).");
        }

        $this->info('Publication migration completed successfully.');
    }
}
