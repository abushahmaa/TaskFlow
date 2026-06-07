<x-app-layout>
    <x-slot name="pageTitle">Tasks</x-slot>

    <div class="card">
        <div class="card-header relative" x-data="{ open: false }">
            <h3 class="section-title">All Tasks</h3>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <button @click="open = !open" type="button" class="btn-secondary relative">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                        @if(request()->anyFilled(['status', 'date_from', 'date_to']))
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 w-2.5 h-2.5 bg-indigo-500 rounded-full border border-white dark:border-slate-900"></span>
                        @endif
                    </button>
                    <!-- Filter Dropdown -->
                    <div x-show="open" @click.away="open = false" style="display: none;" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg z-50 p-4">
                        <form method="GET" action="{{ route('web.tasks.index') }}" class="flex flex-col gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</label>
                                <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Enums\TaskStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Deadline From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Deadline To</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button type="submit" class="btn-primary w-full justify-center">Apply</button>
                                @if(request()->anyFilled(['status', 'date_from', 'date_to']))
                                <a href="{{ route('web.tasks.index') }}" class="btn-secondary w-full justify-center text-center leading-loose">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                @if(auth()->user()->hasAnyRole(['admin', 'project-manager']))
                <a href="{{ route('web.tasks.create') }}" class="btn-primary">Create Task</a>
                @endif
            </div>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="tf-table">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Project</th>
                        <th>Assignee</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $task->name }}</div>
                            </td>
                            <td>
                                <a href="{{ route('web.projects.show', $task->project) }}" class="text-sm text-indigo-600 hover:underline">{{ $task->project->name }}</a>
                            </td>
                            <td>
                                {{ $task->assignee ? $task->assignee->name : 'Unassigned' }}
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
                                <span class="text-sm {{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status->value !== 'completed' ? 'text-red-500 font-medium' : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('web.tasks.show', $task) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">View</a>
                                    @if(auth()->user()->hasAnyRole(['admin', 'project-manager']))
                                        <a href="{{ route('web.tasks.edit', $task) }}" class="text-amber-600 dark:text-amber-400 hover:underline text-sm font-medium">Edit</a>
                                        <form method="POST" action="{{ route('web.tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-500">
                                No tasks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $tasks->links() }}
        </div>
    </div>
</x-app-layout>
