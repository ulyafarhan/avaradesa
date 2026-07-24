<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyGatewayApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-API-Key');
        $expected = config('services.whatsapp.api_key');
        if (!$expected || !hash_equals((string) $expected, (string) ($provided ?? ''))) {
            abort(401, 'Invalid sync key');
        }
        return $next($request);
    }
}
