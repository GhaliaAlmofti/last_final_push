<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'book' => [
                'id' => $this->book->id,
                'title' => $this->book->title,
                'price' => $this->book->price,
            ],
            'qty' => $this->qty,
            'price' => $this->price,
            'subtotal' => $this->qty * $this->price,
        ];
    }
}
