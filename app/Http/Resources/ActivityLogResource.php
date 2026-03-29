<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description ?? '',
            'log_name' => $this->log_name,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'causer' => UserResource::make($this->whenLoaded('causer')),
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
        ];
    }
}
