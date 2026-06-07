<x-app-layout>
    <x-slot name="pageTitle">Create Task</x-slot>

    <div class="mb-6">
        <a href="{{ route('web.tasks.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Tasks
        </a>
    </div>

    <div class="card max-w-3xl">
        <div class="card-header">
            <h2 class="section-title">Create New Task</h2>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert-error mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('web.tasks.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="label">Task Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input">
                </div>

                <div>
                    <label class="label">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="input">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" required class="input">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ (string)old('project_id') === (string)$project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Assign To Employee</label>
                        <select name="assigned_to" class="input">
                            <option value="">Unassigned</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ (string)old('assigned_to') === (string)$employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" required class="input">
                            <option value="">Select Priority</option>
                            @foreach(\App\Enums\TaskPriority::cases() as $priority)
                                <option value="{{ $priority->value }}" {{ old('priority') === $priority->value ? 'selected' : '' }}>{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="input">
                            <option value="">Select Status</option>
                            @foreach(\App\Enums\TaskStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">Deadline <span class="text-red-500">*</span></label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Estimated Hours</label>
                        <input type="number" name="estimated_hours" value="{{ old('estimated_hours') }}" min="1" class="input">
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('web.tasks.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
