<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'total' => $this->total,
            'payment_method' => $this->paymentMethod ? $this->paymentMethod->name : null,
            'address' => $this->address,
            'status' => $this->status->value,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
