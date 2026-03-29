<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'college_id' => $this->college_id,
            'program_id' => $this->program_id,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            
            // Relationships (Transformed)
            'roles' => $this->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'college' => $this->whenLoaded('college', fn() => [
                'id' => $this->college->id,
                'name' => $this->college->name,
                'code' => $this->college->code,
            ]),
            'program' => $this->whenLoaded('program', fn() => [
                'id' => $this->program->id,
                'name' => $this->program->name,
            ]),
            'google_info' => $this->whenLoaded('googleInfo', fn() => [
                'avatar' => $this->googleInfo->avatar,
            ]),
        ];
    }
}
