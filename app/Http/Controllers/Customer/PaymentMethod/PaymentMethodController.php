<?php

namespace App\Http\Controllers\Customer\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    //
        public function index()
    {
        $methods = PaymentMethod::latest()->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $methods
        ], 200);
    }
}
