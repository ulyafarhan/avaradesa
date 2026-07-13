<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TrafficLog;
use Illuminate\Support\Str;

class TrackTraffic
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Abaikan request OPTIONS, rute internal admin, livewire, debugbar, up, dll.
        $path = $request->path();
        if ($request->isMethod('OPTIONS') || 
            $request->is('admin*') ||
            $request->is('livewire*') ||
            $request->is('up') ||
            Str::contains($path, ['telemetry', 'debugbar', '_ignition'])
        ) {
            return $response;
        }

        try {
            $userAgent = $request->userAgent() ?? '';
            $isBot = (bool) preg_match('/(bot|crawl|spider|slurp|crawler|chrome-lighthouse|googlebot|ahrefs|yandex|baidu)/i', $userAgent);

            TrafficLog::create([
                'ip_address' => $request->ip(),
                'user_agent' => substr($userAgent, 0, 500),
                'path' => '/' . ltrim($path, '/'),
                'method' => $request->method(),
                'referer' => substr($request->headers->get('referer') ?? '', 0, 255),
                'is_bot' => $isBot,
            ]);
        } catch (\Throwable $e) {
            // Mencegah crash jika database belum di-migrasi
        }

        return $response;
    }
}
