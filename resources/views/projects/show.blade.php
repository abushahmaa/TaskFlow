<x-app-layout>
    <x-slot name="pageTitle">Project Details</x-slot>

    <div class="mb-6">
        <a href="{{ route('web.projects.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Projects
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="card xl:col-span-2">
            <div class="card-header border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $project->name }}</h2>
                        <p class="text-sm text-slate-500 mt-1">Managed by {{ $project->manager->name }}</p>
                    </div>
                    <span class="badge @if($project->status->value === 'active') badge-emerald @elseif($project->status->value === 'planning') badge-sky @else badge-slate @endif text-base px-3 py-1">
                        {{ $project->status->label() }}
                    </span>
                </div>
            </div>
            
            <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 mb-8">
                {{ $project->description }}
            </div>

            <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-4">Tasks</h3>
            <div class="bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="tf-table w-full">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->tasks as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('web.tasks.show', $task) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $task->name }}
                                    </a>
                                </td>
                                <td>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</td>
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
                                <td><span class="badge badge-slate">{{ $task->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-slate-500">No tasks created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Project Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Start Date</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">End Date</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between mb-1">
                            <span class="text-slate-500">Progress</span>
                            @php
                                $percent = $project->tasks_count > 0 
                                    ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                                    : 0;
                            @endphp
                            <span class="font-medium text-slate-900 dark:text-white">{{ $percent }}%</span>
                        </div>
                        <div class="progress-bar h-2">
                            <div class="progress-fill" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
