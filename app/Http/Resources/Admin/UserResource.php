<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Media\MediaResource;
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'type' => $this->type,
            'status' => $this->status,
            'bio' => $this->bio,
            'country' => $this->country,
            'photo' => MediaResource::make($this->whenLoaded('photo')),
        ];
    }
}
