<x-app-layout>
    <x-slot name="pageTitle">Projects</x-slot>

    <div class="card">
        <div class="card-header relative" x-data="{ open: false }">
            <h3 class="section-title">All Projects</h3>
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
                        <form method="GET" action="{{ route('web.projects.index') }}" class="flex flex-col gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</label>
                                <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Enums\ProjectStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Start Date From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Start Date To</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 dark:text-slate-300 shadow-sm transition-colors">
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button type="submit" class="btn-primary w-full justify-center">Apply</button>
                                @if(request()->anyFilled(['status', 'date_from', 'date_to']))
                                <a href="{{ route('web.projects.index') }}" class="btn-secondary w-full justify-center text-center leading-loose">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('web.projects.create') }}" class="btn-primary">Create Project</a>
                @endif
            </div>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="tf-table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Manager</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $project->name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($project->start_date)->format('M d') }} - 
                                    {{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 flex items-center justify-center text-xs font-semibold">
                                        {{ substr($project->manager->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm">{{ $project->manager->name }}</span>
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
                                            ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                                            : 0;
                                    @endphp
                                    <div class="flex-1 progress-bar">
                                        <div class="progress-fill" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400 w-8 text-right">{{ $percent }}%</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('web.projects.show', $project) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">View</a>
                                    @if(auth()->user()->hasRole('admin'))
                                        <a href="{{ route('web.projects.edit', $project) }}" class="text-amber-600 dark:text-amber-400 hover:underline text-sm font-medium">Edit</a>
                                        <form method="POST" action="{{ route('web.projects.destroy', $project) }}" class="inline" onsubmit="return confirm('Delete this project?');">
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
                            <td colspan="5" class="text-center py-8 text-slate-500">
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>
