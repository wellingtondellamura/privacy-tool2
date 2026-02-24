<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Only members can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->hasMember($user);
    }

    /**
     * Any authenticated user can create a project.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->getMemberRole($user) === 'owner';
    }

    /**
     * Only the owner can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->getMemberRole($user) === 'owner';
    }

    /**
     * Only the owner can invite members.
     */
    public function invite(User $user, Project $project): bool
    {
        return $project->getMemberRole($user) === 'owner';
    }
}
