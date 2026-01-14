<?php

namespace App\Traits;

use App\Models\Media\Media;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

trait HasMedia
{
    public function poster()
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('collection', 'poster');
    }

    public function photo()
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('collection', 'avatar');
    }

    public function uploadMedia($file, string $collection = 'avatar', string $disk = 'public'): Media
    {
        if (in_array($collection, ['avatar', 'cover'])) {
            $this->clearMediaCollection($collection);
        }

        $path = $file->store("uploads/{$collection}", $disk);

        return $this->{$collection === 'gallery' ? 'gallery' : ($collection === 'cover' ? 'cover' : 'photo')}()
            ->create([
                'file_path'  => $path,
                'file_name'  => $file->getClientOriginalName(),
                'mime_type'  => $file->getMimeType(),
                'size'       => $file->getSize(),
                'collection' => $collection,
                'disk'       => $disk,
            ]);
    }

    /**
     * Cleanup: Delete physical files from storage
     */
    public function clearMediaCollection(string $collection): void
    {
        $media = $this->morphMany(Media::class, 'mediable')
            ->where('collection', $collection)
            ->get();

        foreach ($media as $item) {
            Storage::disk($item->disk ?? 'public')->delete($item->file_path);
            $item->delete();
        }
    }
}
