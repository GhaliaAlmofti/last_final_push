<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $appUrl = config('app.url') ?: env('APP_URL', 'http://localhost');
        $apiHost = parse_url($appUrl, PHP_URL_HOST) ?: request()->getHost();

        Route::middleware('api')
            ->domain($apiHost)
            ->prefix('api/v1')
            ->group(base_path('routes/api.php'));

        Route::middleware(['api', 'auth:sanctum'])
            ->domain($apiHost)
            ->prefix('api/v1/admin')
            ->group(base_path('routes/admin.php'));

        Route::middleware(['api', 'auth:sanctum'])
            ->domain($apiHost)
            ->prefix('api/v1/owner')
            ->group(base_path('routes/owner.php'));

        Route::middleware(['api', 'auth:sanctum'])
            ->domain($apiHost)
            ->prefix('api/v1/attendee')
            ->group(base_path('routes/attendee.php'));
    }
}
