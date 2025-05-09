<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Pastikan user terautentikasi
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Pastikan user memiliki role dan cocok dengan salah satu role yang diizinkan
        if (!$request->user()->role || !in_array($request->user()->role->nama_role, $roles)) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
