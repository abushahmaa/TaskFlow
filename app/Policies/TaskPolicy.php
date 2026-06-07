<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager', 'employee']);
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('project-manager')) return $task->project?->manager_id === $user->id;
        if ($user->hasRole('employee')) return $task->assigned_to === $user->id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager']);
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('project-manager')) return $task->project?->manager_id === $user->id;
        // Employees can only change status on their own tasks
        if ($user->hasRole('employee')) return $task->assigned_to === $user->id;
        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager']) &&
            ($user->hasRole('admin') || $task->project?->manager_id === $user->id);
    }

    public function assign(User $user, Task $task): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager']) &&
            ($user->hasRole('admin') || $task->project?->manager_id === $user->id);
    }
}
