<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if (!$user->type || $user->type->value !== $role) {
            return response()->json([
                'data' => null,
                'message' => 'Forbidden: You do not have this .',
                'errors' => ['role' => 'Insufficient permissiorolens']
            ], 403);
        }

        return $next($request);
    }
}
