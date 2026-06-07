<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogWebController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin'), 403);

        $logs = Activity::with('causer')->latest()->paginate(20);
        return view('audit-logs.index', compact('logs'));
    }
}
