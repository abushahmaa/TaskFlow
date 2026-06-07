<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpTaskController extends Controller
{
    /**
     * GET /api/employee/tasks
     * Employee sees ONLY their own assigned tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['project:id,name', 'creator:id,name'])
            ->withCount('workLogs')
            ->assignedTo(Auth::id());

        if ($request->filled('status'))   $query->byStatus($request->status);
        if ($request->filled('priority')) $query->byPriority($request->priority);

        return response()->json(TaskResource::collection($query->paginate(15)));
    }

    /**
     * GET /api/employee/tasks/{task}
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json(new TaskResource(
            $task->load(['project', 'creator', 'workLogs.replies.user'])
        ));
    }

    /**
     * PATCH /api/employee/tasks/{task}/status
     * Employee can only update status of their own task.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:in_progress,in_review,completed'],
        ]);

        $task->update(['status' => $data['status']]);

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties(['new_status' => $data['status']])
            ->log('Employee updated task status');

        return response()->json(new TaskResource($task->refresh()->load(['project', 'creator'])));
    }
}
