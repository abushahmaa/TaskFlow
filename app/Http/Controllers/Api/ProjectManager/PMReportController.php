<?php

namespace App\Http\Controllers\Api\ProjectManager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PMReportController extends Controller
{
    public function __construct(private readonly ProjectProgressService $progress) {}

    /**
     * GET /api/pm/reports/{project}
     * Project-level progress and employee productivity.
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json([
            'report'           => $this->progress->getProjectReport($project),
            'employee_metrics' => $this->progress->getEmployeeMetrics($project),
        ]);
    }

    /**
     * GET /api/pm/reports
     * Summary across all PM's projects.
     */
    public function index(): JsonResponse
    {
        $projects = Project::withCount('tasks')
            ->assignedTo(Auth::id())
            ->get();

        $data = $projects->map(fn ($p) => $this->progress->getProjectReport($p));

        return response()->json(['data' => $data]);
    }
}
