<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project-manager', 'employee']);
    }

    public function view(User $user, WorkLog $workLog): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('project-manager')) {
            return $workLog->task?->project?->manager_id === $user->id;
        }
        // Employee can only see their own logs
        return $workLog->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('employee');
    }

    public function update(User $user, WorkLog $workLog): bool
    {
        return $workLog->user_id === $user->id;
    }

    public function delete(User $user, WorkLog $workLog): bool
    {
        return $user->hasRole('admin') || $workLog->user_id === $user->id;
    }

    public function reply(User $user, WorkLog $workLog): bool
    {
        // Only PMs assigned to this task's project can reply
        return $user->hasAnyRole(['admin', 'project-manager']) &&
            ($user->hasRole('admin') || $workLog->task?->project?->manager_id === $user->id);
    }
}
