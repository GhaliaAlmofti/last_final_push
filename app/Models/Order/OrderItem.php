<?php

namespace App\Models\Order;

use App\Models\Book\Book;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
            'order_id', 'book_id', 'qty', 'price',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

}
