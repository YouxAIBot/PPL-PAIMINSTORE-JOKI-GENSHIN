<?php

use App\Http\Middleware\EnsureNotBanned;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [ForceJsonResponse::class]);
        $middleware->alias(['not.banned' => EnsureNotBanned::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $notFound = fn (Request $request) => $request->is('api/*')
            ? response()->json(['message' => 'Data tidak ditemukan'], 404)
            : null;

        $exceptions->render(fn (ModelNotFoundException $e, Request $request) => $notFound($request));
        $exceptions->render(fn (NotFoundHttpException $e, Request $request) => $notFound($request));
    })->create();
