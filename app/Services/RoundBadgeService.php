<?php

namespace App\Services;

use App\Models\EvaluationRound;
use App\Models\RoundBadge;
use Illuminate\Support\Str;

class RoundBadgeService
{
    /**
     * Create a new badge for a published round.
     * RN-BADGE-01: Eligibility check.
     */
    public function createBadge(EvaluationRound $round): RoundBadge
    {
        if (!$round->isPublished()) {
            throw new \RuntimeException('Round must be published to create a badge.');
        }

        if ($round->publicDirectory?->visibility->value === 'private') {
            throw new \RuntimeException('Private rounds cannot have a public badge.');
        }

        // Revoke existing badge if any (RN-BADGE-06)
        if ($round->badge) {
            $round->badge->delete();
        }

        return RoundBadge::create([
            'evaluation_round_id' => $round->id,
            'public_token' => Str::random(64),
            'style' => 'default',
            'is_active' => true,
        ]);
    }

    /**
     * Revoke a badge.
     */
    public function revokeBadge(RoundBadge $badge): void
    {
        $badge->update(['is_active' => false]);
    }

    /**
     * Update badge style.
     */
    public function updateStyle(RoundBadge $badge, string $style): void
    {
        if (!in_array($style, ['default', 'compact', 'minimal'])) {
            throw new \InvalidArgumentException('Invalid style.');
        }

        $badge->update(['style' => $style]);
    }
}
