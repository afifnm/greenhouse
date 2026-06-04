<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun tidak aktif.'], 403);
            }
            return response()->view('errors.403', ['message' => 'Akun tidak aktif.'], 403);
        }
        if (!in_array($request->user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            return response()->view('errors.403', ['message' => 'Akses ditolak.'], 403);
        }
        return $next($request);
    }
}
