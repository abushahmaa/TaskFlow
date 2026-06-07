<x-app-layout>
    <x-slot name="pageTitle">Admin Dashboard</x-slot>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Projects</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total_projects'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Tasks</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total_tasks'] - $stats['completed_tasks'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Overdue Tasks</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['overdue_tasks'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Employees</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['active_employees'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overdue Tasks -->
        <div class="card">
            <div class="card-header">
                <h3 class="section-title text-red-600 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Recent Overdue Tasks
                </h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overdueTasksList as $task)
                            <tr>
                                <td>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $task->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $task->project->name }}</div>
                                </td>
                                <td>
                                    @if($task->assignee)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-semibold">
                                                {{ substr($task->assignee->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-red-600 dark:text-red-400 font-medium text-sm">
                                        {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-slate-500">
                                    No overdue tasks! 🌟
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="card">
            <div class="card-header">
                <h3 class="section-title">Recent Projects</h3>
                <a href="{{ route('web.projects.index') }}" class="btn-ghost btn-sm">View All</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProjects as $project)
                            <tr>
                                <td>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $project->name }}</div>
                                    <div class="text-xs text-slate-500">PM: {{ $project->manager->name }}</div>
                                </td>
                                <td>
                                    <span class="badge @if($project->status->value === 'active') badge-emerald @elseif($project->status->value === 'planning') badge-sky @else badge-slate @endif">
                                        {{ $project->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-slate-600 dark:text-slate-400">{{ $project->tasks_count }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
