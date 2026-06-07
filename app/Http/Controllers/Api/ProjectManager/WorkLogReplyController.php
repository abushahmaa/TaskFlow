<?php

namespace App\Http\Controllers\Api\ProjectManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkLog\StoreLogReplyRequest;
use App\Http\Resources\LogReplyResource;
use App\Http\Resources\WorkLogResource;
use App\Models\WorkLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkLogReplyController extends Controller
{
    /**
     * GET /api/pm/work-logs/{workLog}
     * View a work log and its replies.
     */
    public function show(WorkLog $workLog): JsonResponse
    {
        $this->authorize('reply', $workLog);

        return response()->json(new WorkLogResource($workLog->load(['user', 'replies.user'])));
    }

    /**
     * POST /api/pm/work-logs/{workLog}/reply
     * PM posts a reply to an employee's work log.
     */
    public function store(StoreLogReplyRequest $request, WorkLog $workLog): JsonResponse
    {
        $this->authorize('reply', $workLog);

        $reply = $workLog->replies()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        activity()
            ->performedOn($workLog)
            ->causedBy(Auth::user())
            ->withProperties(['reply_id' => $reply->id])
            ->log('PM replied to work log');

        return response()->json(new LogReplyResource($reply->load('user')), 201);
    }
}
