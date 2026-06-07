<?php

namespace App\Http\Controllers\Api\ProjectManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PMProjectController extends Controller
{
    /**
     * GET /api/pm/projects
     * Only returns projects assigned to the authenticated PM.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with('manager')
            ->withCount('tasks')
            ->assignedTo(Auth::id());

        if ($request->filled('status')) $query->byStatus($request->status);
        if ($request->filled('search')) $query->where('name', 'like', "%{$request->search}%");

        return response()->json(ProjectResource::collection($query->paginate(15)));
    }

    /**
     * GET /api/pm/projects/{project}
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json(new ProjectResource($project->load('manager')->loadCount('tasks')));
    }

    /**
     * PUT /api/pm/projects/{project}
     * PM can update limited fields (description, status only).
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        // PM can only update description and status
        $project->update($request->only(['description', 'status']));

        return response()->json(new ProjectResource($project->refresh()->load('manager')));
    }
}
