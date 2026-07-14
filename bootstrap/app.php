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
            if ($request->expectsJson() || $request->is('api/*')) {
                report($e);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan server',
                ], 500);
            }
            return $e;
        });
    })->create();
