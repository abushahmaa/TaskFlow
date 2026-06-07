<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\NotificationScheduler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private readonly NotificationScheduler $scheduler) {}

    /**
     * GET /api/admin/projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with('manager')->withCount('tasks')->latest();

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return response()->json(ProjectResource::collection($query->paginate(15)));
    }

    /**
     * POST /api/admin/projects
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json(new ProjectResource($project->load('manager')), 201);
    }

    /**
     * GET /api/admin/projects/{project}
     */
    public function show(Project $project): JsonResponse
    {
        return response()->json(new ProjectResource($project->load('manager')->loadCount('tasks')));
    }

    /**
     * PUT /api/admin/projects/{project}
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json(new ProjectResource($project->refresh()->load('manager')));
    }

    /**
     * DELETE /api/admin/projects/{project}
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();
        return response()->json(['message' => 'Project archived successfully.']);
    }

    /**
     * POST /api/admin/projects/{project}/restore
     */
    public function restore(int $id): JsonResponse
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();
        return response()->json(new ProjectResource($project));
    }
}
