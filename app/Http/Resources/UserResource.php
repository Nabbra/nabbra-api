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
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d g:i'),
            'last_activity_at' => $this->last_activity_at->format('Y-m-d g:i'),
            'last_activity_at_formatted' => $this->last_activity_at->diffForHumans(),
            'created_at' => $this->created_at->format('Y-m-d g:i'),
            'created_at_formatted' => $this->created_at->diffForHumans(),
        ];
    }
}