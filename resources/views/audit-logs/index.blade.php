<x-app-layout>
    <x-slot name="pageTitle">Audit Logs</x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="section-title">System Activity Log</h3>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="tf-table text-sm">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>User</th>
                        <th>Subject Type</th>
                        <th>Subject ID</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                <span class="badge @if($log->event === 'created') badge-emerald @elseif($log->event === 'updated') badge-sky @elseif($log->event === 'deleted') badge-red @else badge-slate @endif uppercase text-xs">
                                    {{ $log->event }}
                                </span>
                                <span class="ml-2 text-slate-700 dark:text-slate-300">{{ $log->description }}</span>
                            </td>
                            <td>
                                @if($log->causer)
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $log->causer->name }}</div>
                                @else
                                    <span class="text-slate-400 italic">System</span>
                                @endif
                            </td>
                            <td class="text-slate-500">{{ class_basename($log->subject_type) ?? '-' }}</td>
                            <td class="text-slate-500">{{ $log->subject_id ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-slate-500">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
