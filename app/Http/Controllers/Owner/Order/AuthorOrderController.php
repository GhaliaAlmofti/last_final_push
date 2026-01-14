<?php

namespace App\Http\Controllers\Author\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\OrderItem;
use Illuminate\Http\Request;

class AuthorOrderController extends Controller
{
    public function index(Request $request)
    {
        $authorId = $request->user()->id;


        $sales = OrderItem::query()
            ->whereHas('book.authors', function ($query) use ($authorId) {
                $query->where('authors.user_id', $authorId);
            })
            ->with([
                'order:id,status,created_at,user_id', 
                'order.user:id,name', 
                'book:id,title,price'  
            ])
            ->latest()
            ->paginate(15);

        $sales->getCollection()->transform(function ($item) {
            return [
                'order_id'    => $item->order_id,
                'book_title'  => $item->book->title,
                'customer'    => $item->order->user->name,
                'quantity'    => $item->qty,
                'price'       => (float) $item->price,
                'subtotal'    => (float) ($item->qty * $item->price),
                'status'      => $item->order->status->value,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $sales->items(),
            'meta'   => [
                'total_sales_count' => $sales->total(),
                'total_revenue'     => $sales->sum(fn($item) => $item['subtotal']),
            ],
            'pagination' => [
                'current_page' => $sales->currentPage(),
                'last_page'    => $sales->lastPage(),
            ]
        ]);
    }
}
