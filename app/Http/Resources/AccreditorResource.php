<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccreditorResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => (bool)$this->is_active,
            'role_status' => $this->role_status,
            'expires_at' => $this->expires_at ? $this->expires_at->toIso8601String() : null,
            'expires_at_human' => $this->expires_at ? $this->expires_at->format('M d, Y') : 'N/A',
            'college_id' => $this->college_id,
            'program_id' => $this->program_id,
            'level' => $this->level,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            
            'college' => $this->whenLoaded('college', fn() => [
                'id' => $this->college->id,
                'name' => $this->college->name,
                'code' => $this->college->code,
            ]),
            'program' => $this->whenLoaded('program', fn() => [
                'id' => $this->program->id,
                'name' => $this->program->name,
            ]),
            'events' => $this->whenLoaded('events', fn() => $this->events->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'expires_at' => $e->expires_at,
            ])),
             'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
        ];
    }
}
