<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'id'         => $this->id,
            'url'        =>$this->full_url,
            'file_name'  => $this->file_name,
            'collection' => $this->collection,
            'mime_type'  => $this->mime_type,
        ];
    }
}