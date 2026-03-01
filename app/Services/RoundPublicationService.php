<?php

namespace App\Services;

use App\Enums\Visibility;
use App\Models\EvaluationRound;
use App\Models\RoundPublication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoundPublicationService
{
    /**
     * Publish or update publication for an evaluation round.
     */
    public function publish(EvaluationRound $round, Visibility $visibility, User $user): RoundPublication
    {
        if (!$round->isClosed()) {
            throw new \InvalidArgumentException("Only closed rounds can be published.");
        }

        $snapshot = $round->snapshots()->latest()->first();
        if (!$snapshot) {
            throw new \RuntimeException("Consolidated snapshot not found for this round.");
        }

        $payload = $snapshot->payload_json;

        return DB::transaction(function () use ($round, $visibility, $user, $payload) {
            $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();
            
            $data = [
                'evaluation_round_id' => $round->id,
                'visibility' => $visibility,
                'published_at' => now(),
                'published_by' => $user->id,
                'score' => $payload['global_score'] ?? 0,
                'medal' => $payload['medal']['name'] ?? null,
                'year' => $round->closed_at?->year ?? now()->year,
                'questionnaire_version_id' => $round->inspections()->first()?->questionnaire_version_id,
            ];

            if (!$publication) {
                // Generate slug only for new publications
                $name = $round->name ?: $round->project->name;
                $data['slug'] = Str::slug($name . '-' . Str::random(6));
                return RoundPublication::create($data);
            }

            $publication->update($data);
            return $publication;
        });
    }

    /**
     * Update the visibility of an existing publication.
     */
    public function updateVisibility(EvaluationRound $round, Visibility $visibility): RoundPublication
    {
        $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();

        if (!$publication) {
            throw new \RuntimeException("No publication found for this round.");
        }

        $publication->update(['visibility' => $visibility]);

        return $publication;
    }

    /**
     * Revoke publication by setting visibility to private.
     */
    public function revoke(EvaluationRound $round): bool
    {
        $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();

        if (!$publication) {
            return true;
        }

        return $publication->update(['visibility' => Visibility::PRIVATE]);
    }
}
