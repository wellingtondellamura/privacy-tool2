<?php

namespace App\Policies;

use App\Models\RoundBadge;
use App\Models\EvaluationRound;
use App\Models\User;

class RoundBadgePolicy
{
    /**
     * Determine whether the user can create a badge for the round.
     */
    public function create(User $user, EvaluationRound $round): bool
    {
        return $round->project->owner_id === $user->id || 
               $round->project->getMemberRole($user) === 'owner';
    }

    /**
     * Determine whether the user can update the badge.
     */
    public function update(User $user, RoundBadge $roundBadge): bool
    {
        return $roundBadge->evaluationRound->project->owner_id === $user->id || 
               $roundBadge->evaluationRound->project->getMemberRole($user) === 'owner';
    }

    /**
     * Determine whether the user can delete the badge.
     */
    public function delete(User $user, RoundBadge $roundBadge): bool
    {
        return $roundBadge->evaluationRound->project->owner_id === $user->id || 
               $roundBadge->evaluationRound->project->getMemberRole($user) === 'owner';
    }
}
