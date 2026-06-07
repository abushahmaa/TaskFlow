<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager']);
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('project-manager')) return $project->manager_id === $user->id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('project-manager')) return $project->manager_id === $user->id;
        return false;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }
}
