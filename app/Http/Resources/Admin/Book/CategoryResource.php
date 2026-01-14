<?php

namespace App\Http\Resources\Admin\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name'=> $this->name,
            'slug'=> $this->slug,
            'order_column' => $this->order_column,

            'children' => CategoryResource::collection(
                $this->whenLoaded('childrenRecursive', function() {
                    return $this->childrenRecursive;
                }, $this->whenLoaded('children'))
            ),
            'parent' => new CategoryResource($this->whenLoaded('parent')),

            'is_root' => is_null($this->parent_id),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
