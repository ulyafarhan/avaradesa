<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\HandleInertiaRequests;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            \App\Http\Middleware\AddCacheControlHeaders::class,
            \App\Http\Middleware\TrackTraffic::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        $middleware->throttleApi();

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource tidak ditemukan',
                ], 404);
            }
            return response($e->getMessage() ?: 'Halaman tidak ditemukan', 404);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak terotentikasi',
                ], 401);
            }
            $guard = !empty($e->guards()) ? $e->guards()[0] : (str_starts_with($request->path(), 'admin/') ? 'admin' : null);
            if ($guard === 'admin') {
                return redirect()->guest('/admin/login');
            }
            return redirect()->guest(route('login'));
        });

        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak memiliki akses',
                ], 403);
            }
            return $e;
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ], 422);
            }
            return $e;
        });

        $exceptions->renderable(function (QueryException $e, Request $request) {
            \App\Services\SystemLogger::log('system.error', 'Database query exception', null, [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                report($e);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan server',
                ], 500);
            }
            return $e;
        });

        $exceptions->reportable(function (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) return false;
            if ($e instanceof \Illuminate\Auth\AuthenticationException) return false;
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) return false;
            if ($e instanceof \Illuminate\Validation\ValidationException) return false;
            if ($e instanceof \Illuminate\Database\QueryException) return false;

            try {
                \App\Services\SystemLogger::log('system.error', 'Unhandled exception: ' . class_basename($e), null, [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            } catch (\Throwable $inner) {
                \Illuminate\Support\Facades\Log::error('SystemLogger failed: ' . $inner->getMessage());
            }
            return false;
        });
    })->create();
