<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'file_path', 'file_name', 'mime_type', 'size', 'collection'
    ];
    
    protected $appends = ['full_url'];


    public function getFullUrlAttribute()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        
        return $disk->url($this->file_path);
    }

    public function mediable() 
    {
        return $this->morphTo();
    }
}
