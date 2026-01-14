<?php

namespace App\Models\Order;

use App\Enums\Order\OrderStatus;
use App\Models\PaymentMethod\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total', 'payment_method_id', 'address', 'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class, 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
