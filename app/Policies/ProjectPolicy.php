<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if ($project->user_id === $user->id) return true;

        // Collaborators can view
        return $project->collaborators()
            ->where('email', $user->email)
            ->whereNotNull('accepted_at')
            ->exists();
    }

    public function update(User $user, Project $project): bool
    {
        if ($project->user_id === $user->id) return true;

        // Editor collaborators can update
        return $project->collaborators()
            ->where('email', $user->email)
            ->where('role', 'editor')
            ->whereNotNull('accepted_at')
            ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}
