<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        if (!auth()->check() || auth()->user()->type->value !== $type) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'errors' => ['You do not have permission to access this resource.']
            ], 403);
        }

        return $next($request);
    }
}
