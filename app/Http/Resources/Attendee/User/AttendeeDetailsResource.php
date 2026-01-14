<?php

namespace App\Http\Resources\Attendee\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'phone_number'=> $this->phone_number,
            'email'=> $this->email
        ];
    }
}
