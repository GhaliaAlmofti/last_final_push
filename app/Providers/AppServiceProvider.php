<?php

namespace App\Providers;

use App\Models\Book\Book;
use App\Policies\BookPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Book::class => BookPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('id', function ($value) {
            return $value; 
        });

        Route::model('event', fn($value) => null);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
