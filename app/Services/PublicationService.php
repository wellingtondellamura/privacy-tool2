<?php

namespace App\Services;

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublicationService
{
    /**
     * Publish or update publication for an inspection.
     */
    public function publish(Inspection $inspection, Visibility $visibility, User $user): InspectionPublication
    {
        if (!$inspection->isClosed()) {
            throw new \InvalidArgumentException("Only closed inspections can be published.");
        }

        $snapshot = $inspection->resultSnapshots()->whereNull('user_id')->first();
        if (!$snapshot) {
            throw new \RuntimeException("Consolidated snapshot not found for this inspection.");
        }

        $payload = $snapshot->payload_json;

        return DB::transaction(function () use ($inspection, $visibility, $user, $payload) {
            return InspectionPublication::updateOrCreate(
                ['inspection_id' => $inspection->id],
                [
                    'visibility' => $visibility,
                    'published_at' => now(),
                    'published_by' => $user->id,
                    'score' => $payload['global_score'] ?? 0,
                    'medal' => $payload['medal']['name'] ?? null,
                    'year' => $inspection->closed_at?->year,
                    'questionnaire_version_id' => $inspection->questionnaire_version_id,
                ]
            );
        });
    }

    /**
     * Update the visibility of an existing publication.
     */
    public function updateVisibility(Inspection $inspection, Visibility $visibility): InspectionPublication
    {
        $publication = $inspection->publication;

        if (!$publication) {
            throw new \RuntimeException("No publication found for this inspection.");
        }

        $publication->update(['visibility' => $visibility]);

        return $publication;
    }

    /**
     * Revoke publication by setting visibility to private.
     */
    public function revoke(Inspection $inspection): bool
    {
        $publication = $inspection->publication;

        if (!$publication) {
            return true; // Already revoked or never published
        }

        return $publication->update(['visibility' => Visibility::PRIVATE]);
    }
}
