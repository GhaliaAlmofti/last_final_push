<?php

namespace App\Http\Resources\Owner\Event;

use App\Http\Resources\Admin\Book\CategoryResource;
use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Owner\User\OwnerResource;
use App\Models\UsersType\Owner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();
        
        $dynamicStatus = match (true) {
            $now->lt($this->start_date) => 'upcoming',
            $now->between($this->start_date, $this->end_date) => 'live',
            default => 'passed'
        };

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date->format('Y-m-d H:i'),
            'end_date' => $this->end_date->format('Y-m-d H:i'),
            
            'status' => $dynamicStatus,
            'max_attendees' => $this->max_attendees,
            
            'status_details' => [
                'raw_status' => $this->status,       
                'dynamic_status' => $dynamicStatus, 
                'is_live' => $dynamicStatus === 'live',
                'is_passed' => $dynamicStatus === 'passed',
            ],
            'owner' => OwnerResource::make($this->whenLoaded('owner')),

            'photo'      => MediaResource::make($this->whenLoaded('poster'))? :null,


            'location'=>$this->location,
            'location_description'=> $this->location_description,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}