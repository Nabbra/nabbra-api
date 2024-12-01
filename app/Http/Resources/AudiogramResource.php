<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudiogramResource extends JsonResource
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
            'type' => $this->type,
            'frequencies' => $this->frequencies,
            'created_at' => $this->created_at->format('Y-m-d g:i'),
            'created_at_formatted' => $this->created_at->diffForHumans(),
        ];
    }
}
