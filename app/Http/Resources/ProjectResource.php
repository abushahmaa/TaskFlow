<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'start_date'            => $this->start_date?->toDateString(),
            'end_date'              => $this->end_date?->toDateString(),
            'status'                => $this->status->value ?? $this->status,
            'status_label'          => $this->status->label() ?? $this->status,
            'completion_percentage' => $this->completion_percentage,
            'manager'               => $this->whenLoaded('manager', fn () => new UserResource($this->manager)),
            'tasks_count'           => $this->whenCounted('tasks'),
            'created_at'            => $this->created_at?->toISOString(),
            'updated_at'            => $this->updated_at?->toISOString(),
        ];
    }
}
