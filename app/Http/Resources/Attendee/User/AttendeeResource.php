<?php

namespace App\Http\Resources\Attendee\User;

use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeResource extends JsonResource
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
            'first_name'=> $this->first_name,
            'last_name'=> $this->last_name,
            'username'=> $this->username,
            'type'=> $this->type, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'photo'      => MediaResource::make($this->whenLoaded('photo'))? :null,

            'attendee_details' => AttendeeDetailsResource::make($this->whenLoaded('attendee')),
        ];
    }
}
