<x-app-layout>
    <x-slot name="pageTitle">Reports & Analytics</x-slot>

    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="card">
            <div class="card-header">
                <h3 class="section-title">Project Completion Rates</h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Total Tasks</th>
                            <th>Completed Tasks</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            @php
                                $percent = $project->tasks_count > 0 
                                    ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                                    : 0;
                            @endphp
                            <tr>
                                <td class="font-medium text-slate-900 dark:text-white">{{ $project->name }}</td>
                                <td><span class="badge @if($project->status->value === 'active') badge-emerald @elseif($project->status->value === 'planning') badge-sky @else badge-slate @endif">{{ $project->status->label() }}</span></td>
                                <td>{{ $project->tasks_count }}</td>
                                <td>{{ $project->completed_tasks_count }}</td>
                                <td class="w-48">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 progress-bar">
                                            <div class="progress-fill" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400 w-8 text-right">{{ $percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-slate-500">No projects available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="section-title">Employee Performance Metrics</h3>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="tf-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Assigned Tasks</th>
                            <th>Completed Tasks</th>
                            <th>Total Hours Logged</th>
                            <th>Productivity Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            @php
                                $prodPercent = $employee->total_assigned_tasks > 0 
                                    ? round(($employee->completed_tasks / $employee->total_assigned_tasks) * 100) 
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-semibold">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $employee->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $employee->total_assigned_tasks }}</td>
                                <td>{{ $employee->completed_tasks }}</td>
                                <td><span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $employee->total_hours_logged ?? 0 }} hrs</span></td>
                                <td class="w-48">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 progress-bar">
                                            <div class="progress-fill" style="width: {{ $prodPercent }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400 w-8 text-right">{{ $prodPercent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-slate-500">No employees available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
