<?php

namespace App\Http\Controllers\Customer\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Order\OrderResource;
use App\Models\Order\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a list of the user's order history.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['paymentMethod']) 
            ->withCount('items')     
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Display the details.
     */

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return new OrderResource($order->load(['items.book', 'paymentMethod']));
    }
}
