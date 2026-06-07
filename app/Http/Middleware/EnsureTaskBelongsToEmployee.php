<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTaskBelongsToEmployee
{
    /**
     * Verify that the task in the route is assigned to the authenticated employee.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $task = $request->route('task');

        if (!$task instanceof Task) {
            $taskId = $request->route('task');
            $task = Task::find($taskId);
        }

        if (!$task || $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Forbidden. This task is not assigned to you.'], 403);
        }

        return $next($request);
    }
}
