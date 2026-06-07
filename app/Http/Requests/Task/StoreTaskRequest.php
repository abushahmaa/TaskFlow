<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id'       => ['required', 'exists:projects,id'],
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'priority'         => ['nullable', Rule::enum(TaskPriority::class)],
            'status'           => ['nullable', Rule::enum(TaskStatus::class)],
            'deadline'         => ['nullable', 'date', 'after:now'],
            'estimated_hours'  => ['nullable', 'numeric', 'min:0.5', 'max:999'],
            'assigned_to'      => ['nullable', 'exists:users,id'],
        ];
    }
}
