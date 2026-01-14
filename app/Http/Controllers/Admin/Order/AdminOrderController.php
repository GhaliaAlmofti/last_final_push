<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\Order\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Enums\Order\OrderStatus as OrderOrderStatus;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Http\JsonResponse;

class AdminOrderController extends Controller
{
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => ['required', new Enum(OrderOrderStatus::class)],
        ]);

        $newStatus = OrderStatus::from($validated['status']);

        if (in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled])) {
            return response()->json([
                'data' => null,
                'message' => "Cannot change status of an order that is already {$order->status->value}.",
                'errors' => ['status' => "Cannot change status of an order that is already {$order->status->value}."]
            ], 422);
        }

        $order->update([
            'status' => $newStatus
        ]);

        return response()->json([
            'data' => [
                'id' => $order->id,
                'current_status' => $order->status->value
            ],
            'message' => "Order status updated to {$newStatus->value}.",
            'errors' => null
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->with(['user:id,name,username', 'paymentMethod'])
            ->latest()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->paginate(15);

        return response()->json([
            'data' => OrderResource::collection($orders),
            'message' => 'Orders retrieved successfully',
            'errors' => null
        ]);
    }

    /**
     * Get orders for a SPECIFIC user.
     */
    public function getUserOrders(User $user)
    {
        $this->authorize('viewAny', Order::class);

        $orders = $user->orders()
            ->with(['paymentMethod'])
            ->latest()
            ->get();

        return response()->json([
            'data' => OrderResource::collection($orders),
            'message' => 'User orders retrieved successfully',
            'errors' => null,
            'user' => [
                'name' => $user->name,
                'username' => $user->username
            ]
        ]);
    }
}
