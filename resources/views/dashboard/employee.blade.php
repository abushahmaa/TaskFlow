<x-app-layout>
    <x-slot name="pageTitle">Employee Dashboard</x-slot>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">My Tasks</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total_tasks'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">In Progress</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['in_progress'] }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Completed</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['completed_tasks'] }}</h3>
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
        <!-- My Active Tasks -->
        <div class="card xl:col-span-2">
            <div class="card-header">
                <h3 class="section-title">My Active Tasks</h3>
                <a href="{{ route('web.tasks.index') }}" class="btn-ghost btn-sm">View All</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTasks as $task)
                            <tr>
                                <td>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $task->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $task->project->name }}</div>
                                </td>
                                <td>
                                    @php
                                        $pClass = match($task->priority->value) {
                                            'critical' => 'badge-red',
                                            'high'     => 'badge-amber',
                                            'medium'   => 'badge-sky',
                                            default    => 'badge-slate'
                                        };
                                    @endphp
                                    <span class="badge {{ $pClass }}">{{ $task->priority->label() }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-slate">{{ $task->status->label() }}</span>
                                </td>
                                <td>
                                    <span class="text-sm {{ \Carbon\Carbon::parse($task->deadline)->isPast() ? 'text-red-500 font-medium' : 'text-slate-600 dark:text-slate-400' }}">
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('M d, Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-500">
                                    No active tasks. You're all caught up!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="card">
            <div class="card-header">
                <h3 class="section-title">Recent Work Logs</h3>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentLogs as $log)
                        <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="badge badge-indigo">{{ $log->hours_worked }} hrs</span>
                                <span class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2 mb-1">
                                {{ $log->description }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">
                                Task: {{ $log->task->name }}
                            </p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            No recent work logs submitted.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
