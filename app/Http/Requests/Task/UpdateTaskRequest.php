<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['sometimes', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'priority'        => ['sometimes', Rule::enum(TaskPriority::class)],
            'status'          => ['sometimes', Rule::enum(TaskStatus::class)],
            'deadline'        => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0.5', 'max:999'],
            'assigned_to'     => ['nullable', 'exists:users,id'],
        ];
    }
}
