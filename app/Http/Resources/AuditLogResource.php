<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $changes = $this->changes();

        return [
            'id'             => $this->id,
            'log_name'       => $this->log_name,
            'event'          => $this->event,
            'description'    => $this->description,
            'subject_type'   => class_basename($this->subject_type ?? ''),
            'subject_id'     => $this->subject_id,
            'causer'         => $this->causer ? [
                'id'   => $this->causer->id,
                'name' => $this->causer->name,
            ] : null,
            'old_values'     => $changes['old'] ?? [],
            'new_values'     => $changes['attributes'] ?? [],
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
