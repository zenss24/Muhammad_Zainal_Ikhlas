<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pemeriksaan logika untuk menentukan apakah pengguna adalah admin atau bukan
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}