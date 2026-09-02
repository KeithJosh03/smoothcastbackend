<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Access Denied. Admin privileges required.'
            ], 403);
        }

        $roleName = $user->role?->role_name ?? '';

        if (strtolower($roleName) !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Access Denied. Admin privileges required.'
            ], 403);
        }

        return $next($request);
    }
}
