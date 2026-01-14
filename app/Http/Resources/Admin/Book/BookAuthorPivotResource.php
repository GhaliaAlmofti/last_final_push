<?php

namespace App\Http\Resources\Admin\Book;

use App\Http\Resources\Admin\Users\Author\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookAuthorPivotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'metadata' => [
                'is_owner'   => (bool) $this->pivot->is_owner,
                'assigned_at' => $this->pivot->created_at?->toDateTimeString(),
            ],
            'details'  => new AuthorResource($this),
        ];
    }
}
