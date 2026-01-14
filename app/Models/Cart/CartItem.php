<?php

namespace App\Models\Cart;

use App\Models\Book\Book;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'book_id', 'qty',
    ];
    
    protected $casts = [
        'qty' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
