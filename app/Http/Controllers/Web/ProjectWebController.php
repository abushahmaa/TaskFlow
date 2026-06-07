<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with('manager')->withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ]);

        // PMs can only see their own projects
        if ($request->user()->hasRole('project-manager')) {
            $query->where('manager_id', $request->user()->id);
        }

        // Apply Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin'), 403, 'Only admins can create projects.');
        $managers = \App\Models\User::role('project-manager')->get();
        return view('projects.create', compact('managers'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,completed,archived',
            'manager_id' => 'required|exists:users,id',
        ]);

        Project::create($validated);
        return redirect()->route('web.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Request $request, Project $project): View
    {
        if ($request->user()->hasRole('project-manager') && $project->manager_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to project.');
        }

        $project->load(['manager', 'tasks.assignee']);
        $project->loadCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Request $request, Project $project): View
    {
        abort_unless($request->user()->hasRole('admin'), 403, 'Only admins can edit projects.');
        $managers = \App\Models\User::role('project-manager')->get();
        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,completed,archived',
            'manager_id' => 'required|exists:users,id',
        ]);

        $project->update($validated);
        return redirect()->route('web.projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $project->delete();
        return redirect()->route('web.projects.index')->with('success', 'Project deleted successfully.');
    }
}
