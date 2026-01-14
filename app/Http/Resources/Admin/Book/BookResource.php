<?php

namespace App\Http\Resources\Admin\Book;
use App\Http\Resources\Admin\Users\UserResource;use App\Http\Resources\Admin\Users\Author\AuthorResource;
use App\Http\Resources\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'isbn'         => $this->isbn,
            'publish_year' => $this->publish_year,
            'stock'        => $this->stock,
            'stock_level'  => $this->stock,
            'status' => [
                'label' => $this->is_active ? 'Active' : 'Inactive',
                'value' => $this->is_active ? 'active' : 'inactive',
            ],
            'price' => [
                'amount'    => $this->price,
                'formatted' => number_format($this->price, 3) . ' LYD',
            ],
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'owner'    => $this->relationLoaded('owner') ? UserResource::make($this->owner->first()) : null,
            'authors'  => UserResource::collection($this->whenLoaded('authors')),
            'cover'    => MediaResource::make($this->whenLoaded('cover')),
        ];
    }
}
