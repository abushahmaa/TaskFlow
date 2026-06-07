<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'task_id'         => $this->task_id,
            'description'     => $this->description,
            'hours_worked'    => $this->hours_worked,
            'has_attachment'  => $this->hasAttachment(),
            'attachment_name' => $this->attachment_name,
            'user'            => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'replies'         => LogReplyResource::collection($this->whenLoaded('replies')),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
