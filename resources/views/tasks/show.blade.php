<x-app-layout>
    <x-slot name="pageTitle">Task Details</x-slot>

    <div class="mb-6">
        <a href="{{ route('web.tasks.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Tasks
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="card xl:col-span-2">
            <div class="card-header border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $task->name }}</h2>
                        <p class="text-sm text-slate-500 mt-1">Project: <a href="{{ route('web.projects.show', $task->project) }}" class="text-indigo-600 hover:underline">{{ $task->project->name }}</a></p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="badge badge-slate">{{ $task->status->label() }}</span>
                        @php
                            $pClass = match($task->priority->value) {
                                'critical' => 'badge-red',
                                'high'     => 'badge-amber',
                                'medium'   => 'badge-sky',
                                default    => 'badge-slate'
                            };
                        @endphp
                        <span class="badge {{ $pClass }}">{{ $task->priority->label() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 mb-8">
                {{ $task->description }}
            </div>

            <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-4">Work Logs</h3>
            <div class="space-y-4">
                @forelse($task->workLogs as $log)
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-semibold">
                                    {{ substr($log->user->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-sm text-slate-900 dark:text-white">{{ $log->user->name }}</span>
                            </div>
                            <span class="badge badge-indigo">{{ $log->hours_worked }} hrs</span>
                        </div>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-2">{{ $log->description }}</p>
                        <p class="text-xs text-slate-400 mt-2">{{ $log->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-500 border border-dashed border-slate-200 dark:border-slate-700 rounded-lg">
                        No work logs submitted yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Task Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Assignee</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Deadline</span>
                        <span class="font-medium {{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status->value !== 'completed' ? 'text-red-500' : 'text-slate-900 dark:text-white' }}">
                            {{ \Carbon\Carbon::parse($task->deadline)->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Est. Hours</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $task->estimated_hours ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
