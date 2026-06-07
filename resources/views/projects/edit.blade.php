<x-app-layout>
    <x-slot name="pageTitle">Edit Project</x-slot>

    <div class="mb-6">
        <a href="{{ route('web.projects.show', $project) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Project
        </a>
    </div>

    <div class="card max-w-3xl">
        <div class="card-header">
            <h2 class="section-title">Edit Project: {{ $project->name }}</h2>
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

            <form action="{{ route('web.projects.update', $project) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="label">Project Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="input">
                </div>

                <div>
                    <label class="label">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="input">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" required class="input">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="label">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="input">
                            @foreach(\App\Enums\ProjectStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $project->status->value) === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Project Manager <span class="text-red-500">*</span></label>
                        <select name="manager_id" required class="input">
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ (string)old('manager_id', $project->manager_id) === (string)$manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('web.projects.show', $project) }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
