<?php

use App\Http\Controllers\Author\Auth\RegisterController;
use App\Http\Controllers\Author\Book\CategoryController;
use App\Http\Controllers\Author\Order\AuthorOrderController;
use App\Http\Controllers\Author\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Owner\Auth\OwnerAuthController;
use App\Http\Controllers\Owner\Event\EventController;
use App\Http\Requests\Author\User\UpdateOwnerMeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [OwnerAuthController::class, 'login']);

Route::middleware(['role:organizer'])->group(function () {
    Route::post('refresh', [OwnerAuthController::class, 'refresh']);
    Route::get('me', [OwnerAuthController::class, 'me']);
    Route::post('me', [OwnerAuthController::class, 'updateProfile']); 
    Route::post('logout', [OwnerAuthController::class, 'logout']);

    Route::get('events', [EventController::class, 'index']);    
    Route::post('events', [EventController::class, 'store']);      
    Route::get('events/{id}', [EventController::class, 'show']);  
    Route::put('events/{id}', [EventController::class, 'update']); 
    Route::delete('events/{id}', [EventController::class, 'destroy']);
});
