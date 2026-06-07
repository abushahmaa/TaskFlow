<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportWebController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Project Completion Report
        $projectQuery = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ]);
        if ($user->hasRole('project-manager')) {
            $projectQuery->where('manager_id', $user->id);
        }
        $projects = $projectQuery->get();

        // Employee Performance Report
        $employeeQuery = User::role('employee')
            ->withCount(['assignedTasks as total_assigned_tasks'])
            ->withCount(['assignedTasks as completed_tasks' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withSum('workLogs as total_hours_logged', 'hours_worked');
            
        // If PM, only show employees assigned to their projects
        if ($user->hasRole('project-manager')) {
            $employeeQuery->whereHas('assignedTasks.project', function ($query) use ($user) {
                $query->where('manager_id', $user->id);
            });
        }
        $employees = $employeeQuery->get();

        return view('reports.index', compact('projects', 'employees'));
    }
}
