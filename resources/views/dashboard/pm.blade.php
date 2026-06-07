<x-app-layout>
    <x-slot name="pageTitle">Project Manager Dashboard</x-slot>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Managed Projects</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['managed_projects'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Tasks</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total_tasks'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Tasks</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['active_tasks'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Overdue</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['overdue_tasks'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- My Projects -->
        <div class="card xl:col-span-2">
            <div class="card-header">
                <h3 class="section-title">My Active Projects</h3>
                <a href="{{ route('web.projects.index') }}" class="btn-ghost btn-sm">View All</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myProjects as $project)
                            <tr>
                                <td>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $project->name }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($project->start_date)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge @if($project->status->value === 'active') badge-emerald @elseif($project->status->value === 'planning') badge-sky @else badge-slate @endif">
                                        {{ $project->status->label() }}
                                    </span>
                                </td>
                                <td class="w-48">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $percent = $project->tasks_count > 0 
                                                ? round(($project->completed_count / $project->tasks_count) * 100) 
                                                : 0;
                                        @endphp
                                        <div class="flex-1 progress-bar">
                                            <div class="progress-fill" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400 w-8 text-right">{{ $percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-slate-500">
                                    No active projects.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="card">
            <div class="card-header">
                <h3 class="section-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Upcoming Deadlines
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($upcomingDeadlines as $task)
                        <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-medium text-sm text-slate-900 dark:text-white truncate pr-4">{{ $task->name }}</h4>
                                <span class="text-xs font-semibold whitespace-nowrap {{ \Carbon\Carbon::parse($task->deadline)->isPast() ? 'text-red-500' : 'text-amber-500' }}">
                                    {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-slate-500">
                                <span class="truncate">{{ $task->project->name }}</span>
                                <span class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                    {{ $task->assignee ? $task->assignee->name : 'Unassigned' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            No upcoming deadlines in the next 7 days! 🎉
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
