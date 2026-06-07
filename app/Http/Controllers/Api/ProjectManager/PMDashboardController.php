<?php

namespace App\Http\Controllers\Api\ProjectManager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PMDashboardController extends Controller
{
    /**
     * GET /api/pm/dashboard
     * KPIs for the authenticated Project Manager.
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $managedProjects = Project::assignedTo($userId)->count();

        // Tasks in PM's projects
        $projectIds = Project::assignedTo($userId)->pluck('id');

        $totalTasks     = Task::whereIn('project_id', $projectIds)->count();
        $activeTasks    = Task::whereIn('project_id', $projectIds)->where('status', 'in_progress')->count();
        $completedTasks = Task::whereIn('project_id', $projectIds)->where('status', 'completed')->count();
        $overdueTasks   = Task::whereIn('project_id', $projectIds)->overdue()->count();

        // Upcoming deadlines (next 7 days)
        $upcomingDeadlines = Task::with(['assignee:id,name', 'project:id,name'])
            ->whereIn('project_id', $projectIds)
            ->dueSoon(168) // 7 days = 168 hours
            ->orderBy('deadline')
            ->limit(10)
            ->get();

        // Task breakdown by status
        $tasksByStatus = Task::whereIn('project_id', $projectIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'kpis' => [
                'managed_projects' => $managedProjects,
                'total_tasks'      => $totalTasks,
                'active_tasks'     => $activeTasks,
                'completed_tasks'  => $completedTasks,
                'overdue_tasks'    => $overdueTasks,
            ],
            'tasks_by_status'    => $tasksByStatus,
            'upcoming_deadlines' => $upcomingDeadlines,
        ]);
    }
}
