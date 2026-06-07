<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'message'     => $this->message,
            'user'        => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
