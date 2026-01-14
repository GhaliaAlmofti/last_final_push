<?php

namespace App\Models\Event;

use App\Enums\Enum\Event\EventLoaction;
use App\Models\Book\Category;
use App\Models\User;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'max_attendees',
        'owner_id',
        'location',
        'location_description'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'location' => EventLoaction::class,
    ];
        public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    //     public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

}
