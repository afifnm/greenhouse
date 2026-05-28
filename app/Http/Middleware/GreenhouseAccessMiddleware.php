<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GreenhouseAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isAdmin()) return $next($request);

        $greenhouse = $request->route('greenhouse');
        if (!$greenhouse) return $next($request);

        $hasAccess = $request->user()->greenhouses()->where('id', $greenhouse->id)->exists();
        if (!$hasAccess) {
            return response()->view('errors.403', ['message' => 'Anda tidak memiliki akses ke greenhouse ini.'], 403);
        }

        return $next($request);
    }
}
