<?php

namespace App\Services;

use App\Models\Project;

class ProjectProgressService
{
    /**
     * Computes task completion percentage for a project.
     */
    public function getCompletionPercentage(Project $project): float
    {
        $total = $project->tasks()->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = $project->tasks()->where('status', 'completed')->count();

        return round(($completed / $total) * 100, 1);
    }

    /**
     * Returns a breakdown of task counts by status for a project.
     */
    public function getStatusBreakdown(Project $project): array
    {
        return $project->tasks()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Computes per-employee metrics for a project.
     */
    public function getEmployeeMetrics(Project $project): array
    {
        return $project->tasks()
            ->with('assignee:id,name,email')
            ->whereNotNull('assigned_to')
            ->get()
            ->groupBy('assigned_to')
            ->map(function ($tasks, $userId) {
                $user = $tasks->first()->assignee;
                $totalHours = $tasks->sum(fn ($t) => $t->workLogs->sum('hours_worked'));

                return [
                    'user_id'         => $userId,
                    'name'            => $user?->name,
                    'email'           => $user?->email,
                    'assigned_tasks'  => $tasks->count(),
                    'completed_tasks' => $tasks->where('status', 'completed')->count(),
                    'total_hours'     => round($totalHours, 2),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Returns a report summary for a project.
     */
    public function getProjectReport(Project $project): array
    {
        $project->loadCount('tasks');
        $completedCount = $project->tasks()->where('status', 'completed')->count();
        $pendingCount   = $project->tasks()->where('status', '!=', 'completed')->count();

        return [
            'project_id'            => $project->id,
            'project_name'          => $project->name,
            'completion_percentage' => $this->getCompletionPercentage($project),
            'total_tasks'           => $project->tasks_count,
            'completed_tasks'       => $completedCount,
            'pending_tasks'         => $pendingCount,
            'status_breakdown'      => $this->getStatusBreakdown($project),
        ];
    }
}
