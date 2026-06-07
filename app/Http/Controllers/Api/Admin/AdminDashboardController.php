<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard
     * Aggregated KPIs for the admin dashboard.
     */
    public function index(): JsonResponse
    {
        $totalProjects    = Project::count();
        $activeProjects   = Project::byStatus('active')->count();
        $totalTasks       = Task::count();
        $completedTasks   = Task::where('status', 'completed')->count();
        $overdueTasks     = Task::overdue()->count();
        $activeEmployees  = User::role('employee')->where('is_active', true)->count();
        $totalUsers       = User::count();

        // Tasks by status
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Projects by status
        $projectsByStatus = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Recent 5 overdue tasks
        $overdueTasksList = Task::with(['project:id,name', 'assignee:id,name'])
            ->overdue()
            ->orderBy('deadline')
            ->limit(5)
            ->get(['id', 'name', 'priority', 'status', 'deadline', 'project_id', 'assigned_to']);

        return response()->json([
            'kpis' => [
                'total_projects'   => $totalProjects,
                'active_projects'  => $activeProjects,
                'total_tasks'      => $totalTasks,
                'completed_tasks'  => $completedTasks,
                'overdue_tasks'    => $overdueTasks,
                'pending_tasks'    => $totalTasks - $completedTasks,
                'active_employees' => $activeEmployees,
                'total_users'      => $totalUsers,
                'completion_rate'  => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            ],
            'tasks_by_status'    => $tasksByStatus,
            'projects_by_status' => $projectsByStatus,
            'recent_overdue'     => $overdueTasksList,
        ]);
    }
}
