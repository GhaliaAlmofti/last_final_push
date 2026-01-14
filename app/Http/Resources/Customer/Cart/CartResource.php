<?php

namespace App\Http\Resources\Customer\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'address'        => $this->address,
            'payment_method' => $this->paymentMethod ? $this->paymentMethod->name : null,
            'items'          => CartItemResource::collection($this->whenLoaded('items')),
            'summary' => [
                'item_count'  => $this->items->sum('qty'),
                'grand_total' => $this->items->sum(function ($item) {
                    return $item->qty * $item->book->price;
                }),
            ]
        ];
    }
}
