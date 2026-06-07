<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkLog\StoreWorkLogRequest;
use App\Http\Resources\WorkLogResource;
use App\Models\Task;
use App\Models\WorkLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkLogController extends Controller
{
    /**
     * GET /api/employee/tasks/{task}/logs
     * Employee views their own work logs on a task.
     */
    public function index(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $logs = WorkLog::with(['user', 'replies.user'])
            ->where('task_id', $task->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return response()->json(WorkLogResource::collection($logs));
    }

    /**
     * POST /api/employee/tasks/{task}/logs
     * Employee submits a new work log (with optional file attachment).
     */
    public function store(StoreWorkLogRequest $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $data = [
            'task_id'      => $task->id,
            'user_id'      => Auth::id(),
            'description'  => $request->description,
            'hours_worked' => $request->hours_worked,
        ];

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("work-logs/{$task->id}", 'local');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $log = WorkLog::create($data);

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties(['log_id' => $log->id, 'hours' => $log->hours_worked])
            ->log('Employee submitted work log');

        return response()->json(new WorkLogResource($log->load(['user', 'replies'])), 201);
    }

    /**
     * GET /api/employee/logs/{workLog}
     * View a single work log with replies.
     */
    public function show(WorkLog $workLog): JsonResponse
    {
        $this->authorize('view', $workLog);

        return response()->json(new WorkLogResource($workLog->load(['user', 'replies.user'])));
    }

    /**
     * GET /api/employee/logs/{workLog}/attachment
     * Download the attachment from a work log.
     */
    public function downloadAttachment(WorkLog $workLog): mixed
    {
        $this->authorize('view', $workLog);

        if (!$workLog->attachment_path) {
            return response()->json(['message' => 'No attachment found.'], 404);
        }

        return Storage::disk('local')->download(
            $workLog->attachment_path,
            $workLog->attachment_name ?? 'attachment'
        );
    }
}
