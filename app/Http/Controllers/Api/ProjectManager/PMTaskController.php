<?php

namespace App\Http\Controllers\Api\ProjectManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationScheduler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PMTaskController extends Controller
{
    public function __construct(private readonly NotificationScheduler $scheduler) {}

    /**
     * GET /api/pm/projects/{project}/tasks
     */
    public function index(Request $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $query = Task::with(['assignee', 'creator'])->withCount('workLogs')
            ->forProject($project->id);

        if ($request->filled('status'))   $query->byStatus($request->status);
        if ($request->filled('priority')) $query->byPriority($request->priority);
        if ($request->filled('assigned_to')) $query->assignedTo($request->assigned_to);

        return response()->json(TaskResource::collection($query->paginate(15)));
    }

    /**
     * POST /api/pm/projects/{project}/tasks
     */
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $validated = array_merge($request->validated(), [
            'project_id' => $project->id,
            'created_by' => Auth::id(),
        ]);

        $task = Task::create($validated);

        if ($task->deadline) {
            $this->scheduler->scheduleReminders($task);
        }

        return response()->json(new TaskResource($task->load(['project', 'assignee', 'creator'])), 201);
    }

    /**
     * PUT /api/pm/projects/{project}/tasks/{task}
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        if ($task->wasChanged('deadline') && $task->deadline) {
            $this->scheduler->scheduleReminders($task);
        }

        return response()->json(new TaskResource($task->refresh()->load(['project', 'assignee', 'creator'])));
    }

    /**
     * DELETE /api/pm/projects/{project}/tasks/{task}
     */
    public function destroy(Project $project, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted.']);
    }

    /**
     * POST /api/pm/projects/{project}/tasks/{task}/assign
     */
    public function assign(AssignTaskRequest $request, Project $project, Task $task): JsonResponse
    {
        $this->authorize('assign', $task);

        $employee = User::findOrFail($request->user_id);
        if (!$employee->hasRole('employee')) {
            return response()->json(['message' => 'User must be an employee.'], 422);
        }

        $task->update(['assigned_to' => $employee->id]);

        if ($task->deadline) {
            $this->scheduler->scheduleReminders($task->fresh());
        }

        return response()->json(new TaskResource($task->refresh()->load(['project', 'assignee'])));
    }
}
