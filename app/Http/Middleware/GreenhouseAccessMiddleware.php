<?php

namespace App\Http\Middleware;

use App\Models\Greenhouse;
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

        // Load model if route binding didn't resolve it
        if (is_string($greenhouse)) {
            $greenhouse = Greenhouse::find($greenhouse);
            if (!$greenhouse) {
                abort(404);
            }
        }

        $hasAccess = $request->user()->greenhouses()->where('id', $greenhouse->id)->exists();
        if (!$hasAccess) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke greenhouse ini.'], 403);
            }
            return response()->view('errors.403', ['message' => 'Anda tidak memiliki akses ke greenhouse ini.'], 403);
        }

        return $next($request);
    }
}
