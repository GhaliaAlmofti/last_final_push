<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\PaymentMethodResource;
use App\Models\PaymentMethod\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    /**+
     * Display a listing of the resource.
     */
    public function index()
    {
        $methods = PaymentMethod::latest()->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $methods
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:payment_methods,name',
        ]);

        $paymentMethod = PaymentMethod::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment method created.',
            'data'    => $paymentMethod
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $paymentMethod
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100', Rule::unique('payment_methods')->ignore($paymentMethod->id)],
        ]);

        $paymentMethod->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment method updated.',
            'data'    => $paymentMethod
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->orders()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete payment method with existing orders. Disable it instead.'
            ], 422);
        }

        $paymentMethod->delete();

        return response()->json(null, 204);
    }
}
