<?php

namespace App\Models\PaymentMethod;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name'
    ];

        public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

