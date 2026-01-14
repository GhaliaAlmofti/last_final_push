<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\Customer\Cart\CartResource;
use Illuminate\Http\Request;
use App\Models\Book\Book;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /*
    index
    */
    public function index(Request $request)
    {
        $cart = $request->user()->cart()
            ->with('items.book')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'data' => null,
                'message' => 'Your cart is currently empty.',
                'errors' => null
            ]);
        }

        return response()->json([
            'data' => new CartResource($cart),
            'message' => 'Cart retrieved successfully',
            'errors' => null
        ]);
    }
    /**
     * Add or Update item in the single user cart.
     */
    public function update(UpdateCartRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated) {
            $user = $request->user();

            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            if ($validated['qty'] == 0) {
                $cart->items()->where('book_id', $validated['book_id'])->delete();
                return response()->json([
                    'data' => null,
                    'message' => 'Item removed from cart.',
                    'errors' => null
                ]);
            }

            $book = Book::findOrFail($validated['book_id']);
            if ($book->stock < $validated['qty']) {
                return response()->json([
                    'data' => null,
                    'message' => "Insufficient stock. Only {$book->stock} left.",
                    'errors' => ['stock' => "Insufficient stock. Only {$book->stock} left."]
                ], 422);
            }

            $cart->items()->updateOrCreate(
                ['book_id' => $validated['book_id']],
                ['qty' => $validated['qty']]
            );

            return response()->json([
                'data' => new CartResource($cart->load('items.book')),
                'message' => 'Cart updated successfully',
                'errors' => null
            ]);
        });
    }
}
