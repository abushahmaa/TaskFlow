<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('project-manager')) {
            return $this->pmDashboard();
        }

        return $this->employeeDashboard();
    }

    private function adminDashboard()
    {
        $stats = [
            'total_projects'   => Project::count(),
            'active_projects'  => Project::where('status', 'active')->count(),
            'total_tasks'      => Task::count(),
            'completed_tasks'  => Task::where('status', 'completed')->count(),
            'overdue_tasks'    => Task::where('deadline', '<', now())->whereNotIn('status', ['completed'])->count(),
            'active_employees' => User::role('employee')->where('is_active', true)->count(),
        ];

        $overdueTasksList = Task::with(['project:id,name', 'assignee:id,name'])
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['completed'])
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $recentProjects = Project::with('manager:id,name')
            ->withCount('tasks')
            ->latest()
            ->limit(5)
            ->get();

        $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard.admin', compact('stats', 'overdueTasksList', 'recentProjects', 'tasksByStatus'));
    }

    private function pmDashboard()
    {
        $userId     = Auth::id();
        $projectIds = Project::where('manager_id', $userId)->pluck('id');

        $stats = [
            'managed_projects' => $projectIds->count(),
            'total_tasks'      => Task::whereIn('project_id', $projectIds)->count(),
            'active_tasks'     => Task::whereIn('project_id', $projectIds)->where('status', 'in_progress')->count(),
            'overdue_tasks'    => Task::whereIn('project_id', $projectIds)->where('deadline', '<', now())->whereNotIn('status', ['completed'])->count(),
        ];

        $upcomingDeadlines = Task::with(['assignee:id,name', 'project:id,name'])
            ->whereIn('project_id', $projectIds)
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->whereNotIn('status', ['completed'])
            ->orderBy('deadline')
            ->limit(8)
            ->get();

        $myProjects = Project::withCount(['tasks', 'tasks as completed_count' => fn ($q) => $q->where('status', 'completed')])
            ->where('manager_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.pm', compact('stats', 'upcomingDeadlines', 'myProjects'));
    }

    private function employeeDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'total_tasks'     => Task::where('assigned_to', $userId)->count(),
            'completed_tasks' => Task::where('assigned_to', $userId)->where('status', 'completed')->count(),
            'overdue_tasks'   => Task::where('assigned_to', $userId)->where('deadline', '<', now())->whereNotIn('status', ['completed'])->count(),
            'in_progress'     => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
        ];

        $dueSoon = Task::with('project:id,name')
            ->where('assigned_to', $userId)
            ->whereBetween('deadline', [now(), now()->addDays(3)])
            ->whereNotIn('status', ['completed'])
            ->orderBy('deadline')
            ->get();

        $myTasks = Task::with('project:id,name')
            ->where('assigned_to', $userId)
            ->whereNotIn('status', ['completed'])
            ->latest()
            ->limit(5)
            ->get();

        $recentLogs = Auth::user()->workLogs()
            ->with('task:id,name')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.employee', compact('stats', 'dueSoon', 'myTasks', 'recentLogs'));
    }
}
