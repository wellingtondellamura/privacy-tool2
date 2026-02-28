<?php

namespace App\Policies;

use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Models\User;

class PublicationPolicy
{
    /**
     * Only project owner can manage publications.
     */
    private function isOwner(User $user, Inspection $inspection): bool
    {
        return $inspection->project->getMemberRole($user) === 'owner';
    }

    public function view(User $user, InspectionPublication $publication): bool
    {
        return $this->isOwner($user, $publication->inspection);
    }

    public function create(User $user, Inspection $inspection): bool
    {
        // RN-PUB-01: status = closed
        // RN-PUB-02: role = owner
        return $inspection->isClosed() && $this->isOwner($user, $inspection);
    }

    public function update(User $user, InspectionPublication $publication): bool
    {
        return $this->isOwner($user, $publication->inspection);
    }

    public function delete(User $user, InspectionPublication $publication): bool
    {
        return $this->isOwner($user, $publication->inspection);
    }
}
