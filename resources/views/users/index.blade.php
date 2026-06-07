<x-app-layout>
    <x-slot name="pageTitle">Users Management</x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="section-title">System Users</h3>
            <button class="btn-primary">Add User</button>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="tf-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge @if($role->name === 'admin') badge-red @elseif($role->name === 'project-manager') badge-indigo @else badge-slate @endif">
                                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-slate-600 dark:text-slate-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
