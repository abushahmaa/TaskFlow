<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationScheduler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(private readonly NotificationScheduler $scheduler) {}

    /**
     * GET /api/admin/tasks
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['project', 'assignee', 'creator'])->withCount('workLogs')->latest();

        if ($request->filled('project_id')) $query->forProject($request->project_id);
        if ($request->filled('status'))     $query->byStatus($request->status);
        if ($request->filled('priority'))   $query->byPriority($request->priority);
        if ($request->filled('assigned_to')) $query->assignedTo($request->assigned_to);
        if ($request->filled('deadline_from')) $query->where('deadline', '>=', $request->deadline_from);
        if ($request->filled('deadline_to'))   $query->where('deadline', '<=', $request->deadline_to);
        if ($request->filled('search'))     $query->where('name', 'like', "%{$request->search}%");

        return response()->json(TaskResource::collection($query->paginate(15)));
    }

    /**
     * POST /api/admin/tasks
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create(array_merge($request->validated(), ['created_by' => Auth::id()]));

        // Schedule deadline reminders if deadline is set
        if ($task->deadline) {
            $this->scheduler->scheduleReminders($task);
        }

        return response()->json(new TaskResource($task->load(['project', 'assignee', 'creator'])), 201);
    }

    /**
     * GET /api/admin/tasks/{task}
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json(new TaskResource($task->load(['project', 'assignee', 'creator'])->loadCount('workLogs')));
    }

    /**
     * PUT /api/admin/tasks/{task}
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        if ($task->wasChanged('deadline') && $task->deadline) {
            $this->scheduler->scheduleReminders($task);
        }

        return response()->json(new TaskResource($task->refresh()->load(['project', 'assignee', 'creator'])));
    }

    /**
     * DELETE /api/admin/tasks/{task}
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully.']);
    }

    /**
     * POST /api/admin/tasks/{task}/assign
     */
    public function assign(AssignTaskRequest $request, Task $task): JsonResponse
    {
        $employee = User::findOrFail($request->user_id);

        if (!$employee->hasRole('employee')) {
            return response()->json(['message' => 'User must have the employee role.'], 422);
        }

        $task->update(['assigned_to' => $employee->id, 'overdue_notified' => false]);

        if ($task->deadline) {
            $this->scheduler->scheduleReminders($task);
        }

        return response()->json(new TaskResource($task->refresh()->load(['project', 'assignee', 'creator'])));
    }
}
