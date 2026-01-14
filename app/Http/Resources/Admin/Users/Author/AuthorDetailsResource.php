<?php

namespace App\Http\Resources\Admin\Users\Author;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bio'     => $this->bio,
            'country' => $this->country,
        ];
    }
}
