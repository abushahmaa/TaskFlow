<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\ProjectProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ProjectProgressService $progress) {}

    /**
     * GET /api/admin/reports/projects
     * Full project completion report.
     */
    public function projectReport(Request $request): JsonResponse
    {
        $query = Project::withCount('tasks');

        if ($request->filled('status')) $query->byStatus($request->status);

        $projects = $query->get();

        $report = $projects->map(fn ($p) => $this->progress->getProjectReport($p));

        return response()->json(['data' => $report]);
    }

    /**
     * GET /api/admin/reports/employees
     * Employee productivity report.
     */
    public function employeeReport(Request $request): JsonResponse
    {
        $query = User::role('employee')->withCount(['assignedTasks as total_tasks']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$term%")->orWhere('email', 'like', "%$term%"));
        }

        $employees = $query->get()->map(function ($user) {
            $completedTasks = $user->assignedTasks()->where('status', 'completed')->count();
            $totalHours     = $user->workLogs()->sum('hours_worked');

            // Avg completion time in hours
            $completedTasksData = $user->assignedTasks()
                ->where('status', 'completed')
                ->whereNotNull('deadline')
                ->get();

            $avgHours = null;
            if ($completedTasksData->isNotEmpty()) {
                $totalHours2 = $completedTasksData->sum(fn ($t) =>
                    $t->created_at->diffInHours($t->updated_at)
                );
                $avgHours = round($totalHours2 / $completedTasksData->count(), 1);
            }

            return [
                'user_id'          => $user->id,
                'name'             => $user->name,
                'email'            => $user->email,
                'total_tasks'      => $user->total_tasks,
                'completed_tasks'  => $completedTasks,
                'pending_tasks'    => $user->total_tasks - $completedTasks,
                'total_hours_logged' => round($totalHours, 2),
                'avg_completion_hours' => $avgHours,
            ];
        });

        return response()->json(['data' => $employees]);
    }

    /**
     * GET /api/admin/reports/projects/{project}
     * Single project report.
     */
    public function singleProjectReport(Project $project): JsonResponse
    {
        $project->loadCount('tasks');
        return response()->json([
            'report'          => $this->progress->getProjectReport($project),
            'employee_metrics' => $this->progress->getEmployeeMetrics($project),
        ]);
    }
}
