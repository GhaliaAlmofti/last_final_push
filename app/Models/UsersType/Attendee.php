<?php

namespace App\Models\UsersType;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $primaryKey = 'user_id';
    protected $table = 'attendees';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 
        'phone_number', 
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
