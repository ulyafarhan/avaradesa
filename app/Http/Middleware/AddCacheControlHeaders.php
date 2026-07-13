<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCacheControlHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            return $response;
        }

        if ($response->isRedirection() || $response->isServerError()) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html') && !$request->header('X-Inertia')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === null) {
            return $response;
        }

        $etag = '"' . md5($content) . '"';
        $response->headers->set('ETag', $etag);
        $response->headers->set('Cache-Control', 'private, must-revalidate');

        if ($request->header('If-None-Match') === $etag) {
            $response->setContent(null);
            $response->setStatusCode(304);
        }

        return $response;
    }
}
