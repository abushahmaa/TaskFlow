<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with(['project', 'assignee']);

        if ($request->user()->hasRole('employee')) {
            $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->hasRole('project-manager')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('manager_id', $request->user()->id);
            });
        }

        // Apply Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('deadline', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('deadline', '<=', $request->date_to);
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'project-manager']), 403);
        
        $projectsQuery = \App\Models\Project::query();
        if ($request->user()->hasRole('project-manager')) {
            $projectsQuery->where('manager_id', $request->user()->id);
        }
        $projects = $projectsQuery->get();
        $employees = \App\Models\User::role('employee')->get();
        
        return view('tasks.create', compact('projects', 'employees'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'project-manager']), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:to-do,in-progress,in-review,completed,blocked',
            'deadline' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|integer|min:1',
        ]);
        
        // Ensure PMs can only create tasks for their projects
        if ($request->user()->hasRole('project-manager')) {
            $project = \App\Models\Project::findOrFail($validated['project_id']);
            abort_unless($project->manager_id === $request->user()->id, 403);
        }

        $validated['created_by'] = $request->user()->id;
        Task::create($validated);
        
        return redirect()->route('web.tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Request $request, Task $task): View
    {
        // Employee can only view assigned tasks
        if ($request->user()->hasRole('employee') && $task->assigned_to !== $request->user()->id) {
            abort(403);
        }

        // PM can only view tasks for their projects
        if ($request->user()->hasRole('project-manager') && $task->project->manager_id !== $request->user()->id) {
            abort(403);
        }

        $task->load(['project', 'assignee', 'workLogs.user']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Request $request, Task $task): View
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'project-manager']), 403);
        
        if ($request->user()->hasRole('project-manager') && $task->project->manager_id !== $request->user()->id) {
            abort(403);
        }
        
        $projectsQuery = \App\Models\Project::query();
        if ($request->user()->hasRole('project-manager')) {
            $projectsQuery->where('manager_id', $request->user()->id);
        }
        $projects = $projectsQuery->get();
        $employees = \App\Models\User::role('employee')->get();
        
        return view('tasks.edit', compact('task', 'projects', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'project-manager']), 403);
        
        if ($request->user()->hasRole('project-manager') && $task->project->manager_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:to-do,in-progress,in-review,completed,blocked',
            'deadline' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|integer|min:1',
        ]);

        $task->update($validated);
        return redirect()->route('web.tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task)
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'project-manager']), 403);
        
        if ($request->user()->hasRole('project-manager') && $task->project->manager_id !== $request->user()->id) {
            abort(403);
        }
        
        $task->delete();
        return redirect()->route('web.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
