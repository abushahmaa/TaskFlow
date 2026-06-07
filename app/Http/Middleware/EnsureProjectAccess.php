<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectAccess
{
    /**
     * Ensure a Project Manager is assigned to the project in the route.
     * Admins always pass through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Admins bypass this check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        $project = $request->route('project');

        if (!$project instanceof Project) {
            $projectId = $request->route('project');
            $project = Project::find($projectId);
        }

        if (!$project || $project->manager_id !== $user->id) {
            return response()->json(['message' => 'Forbidden. You are not assigned to this project.'], 403);
        }

        return $next($request);
    }
}
