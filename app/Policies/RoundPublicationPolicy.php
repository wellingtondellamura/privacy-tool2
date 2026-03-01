<?php

namespace App\Policies;

use App\Models\EvaluationRound;
use App\Models\RoundPublication;
use App\Models\User;

class RoundPublicationPolicy
{
    private function isOwner(User $user, EvaluationRound $round): bool
    {
        return $round->project->getMemberRole($user) === 'owner';
    }

    public function create(User $user, EvaluationRound $round): bool
    {
        // Only closed rounds can be published, and only by project owner
        return $round->isClosed() && $this->isOwner($user, $round);
    }

    public function update(User $user, RoundPublication $publication): bool
    {
        return $this->isOwner($user, $publication->evaluationRound);
    }

    public function delete(User $user, RoundPublication $publication): bool
    {
        return $this->isOwner($user, $publication->evaluationRound);
    }
}
