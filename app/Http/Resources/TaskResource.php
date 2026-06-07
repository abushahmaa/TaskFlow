<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'priority'         => $this->priority->value ?? $this->priority,
            'priority_label'   => $this->priority->label() ?? $this->priority,
            'status'           => $this->status->value ?? $this->status,
            'status_label'     => $this->status->label() ?? $this->status,
            'deadline'         => $this->deadline?->toISOString(),
            'estimated_hours'  => $this->estimated_hours,
            'is_overdue'       => $this->deadline && $this->deadline->isPast() && $this->status->value !== 'completed',
            'project'          => $this->whenLoaded('project', fn () => new ProjectResource($this->project)),
            'assignee'         => $this->whenLoaded('assignee', fn () => new UserResource($this->assignee)),
            'creator'          => $this->whenLoaded('creator', fn () => new UserResource($this->creator)),
            'work_logs_count'  => $this->whenCounted('workLogs'),
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}
