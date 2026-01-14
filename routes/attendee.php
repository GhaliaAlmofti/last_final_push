<?php

use App\Http\Controllers\Admin\Order\AdminOrderController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\Customer\Book\CategoryBookController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\PaymentMethod\PaymentMethodController;
use Illuminate\Support\Facades\Route;


// Route::post('register',[RegisterController::class,'register']);
 
// Route::apiResource('payment-methods', PaymentMethodController::class)->only(['index']);

// Route::apiResource('categories', CategoryBookController::class)->only(['index','show']);

// Route::prefix('cart')->group(function () {
//     Route::get('/', [CartController::class, 'index']);     
//     Route::post('/', [CartController::class, 'update']); 
// });
