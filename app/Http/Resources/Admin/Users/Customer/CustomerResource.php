<?php

namespace App\Http\Resources\Admin\Users\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'username'=> $this->username,
            'full_name'=> $this->name,
            'type'=> $this->type, 
            'status'=>$this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'customer_details' => CustomerDetailsResource::make($this->whenLoaded('customer')),
        ];
    }
}
