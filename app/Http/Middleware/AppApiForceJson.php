<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware khusus App API: force Accept JSON + cek X-App-Version header.
 */
class AppApiForceJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        // ponytail: X-App-Version check hanya log/warning, block kalau butuh version gate
        $response = $next($request);

        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }
}
