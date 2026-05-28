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
            return response()->view('errors.403', ['message' => 'Akun tidak aktif.'], 403);
        }
        if (!in_array($request->user()->role, $roles)) {
            return response()->view('errors.403', ['message' => 'Akses ditolak.'], 403);
        }
        return $next($request);
    }
}
