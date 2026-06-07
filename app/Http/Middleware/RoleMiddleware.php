<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Checks that the authenticated user has one of the given Spatie roles.
     *
     * Usage in routes: ->middleware('role:admin')
     *              or: ->middleware('role:admin|project-manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Support pipe-separated roles: 'role:admin|project-manager'
        foreach ($roles as $role) {
            $parts = explode('|', $role);
            foreach ($parts as $r) {
                if ($user->hasRole(trim($r))) {
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Forbidden. Insufficient role.'], 403);
    }
}
