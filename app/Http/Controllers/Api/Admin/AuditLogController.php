<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * GET /api/admin/audit-logs
     * Paginated audit log with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                  ->where('causer_type', 'App\\Models\\User');
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        return response()->json(AuditLogResource::collection($query->paginate(20)));
    }

    /**
     * GET /api/admin/audit-logs/{id}
     */
    public function show(int $id): JsonResponse
    {
        $log = Activity::with('causer')->findOrFail($id);
        return response()->json(new AuditLogResource($log));
    }
}
