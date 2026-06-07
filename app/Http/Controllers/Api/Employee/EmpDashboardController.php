<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EmpDashboardController extends Controller
{
    /**
     * GET /api/employee/dashboard
     * Employee KPIs and activity summary.
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $totalTasks     = Task::assignedTo($userId)->count();
        $completedTasks = Task::assignedTo($userId)->where('status', 'completed')->count();
        $inProgressTasks = Task::assignedTo($userId)->where('status', 'in_progress')->count();
        $overdueTasks   = Task::assignedTo($userId)->overdue()->count();

        // Tasks due in next 48h
        $dueSoon = Task::with(['project:id,name'])
            ->assignedTo($userId)
            ->dueSoon(48)
            ->orderBy('deadline')
            ->get(['id', 'name', 'priority', 'status', 'deadline', 'project_id']);

        // Recent 5 work logs
        $recentLogs = auth()->user()->workLogs()
            ->with('task:id,name')
            ->latest()
            ->limit(5)
            ->get(['id', 'task_id', 'hours_worked', 'description', 'created_at']);

        // Task breakdown by status
        $tasksByStatus = Task::assignedTo($userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'kpis' => [
                'total_tasks'       => $totalTasks,
                'completed_tasks'   => $completedTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks'     => $overdueTasks,
                'pending_tasks'     => $totalTasks - $completedTasks,
                'completion_rate'   => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            ],
            'tasks_due_soon'  => $dueSoon,
            'recent_logs'     => $recentLogs,
            'tasks_by_status' => $tasksByStatus,
        ]);
    }
}
