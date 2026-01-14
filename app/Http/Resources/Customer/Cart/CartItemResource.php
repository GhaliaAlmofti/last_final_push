<?php

namespace App\Http\Resources\Customer\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'subtotal' => $this->qty * $this->book->price,
        ];
    }
}
