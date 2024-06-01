<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/v1')
                ->name('api.v1.')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            \App\Http\Middleware\ValidateJsonApiHeaders::class,
            \App\Http\Middleware\ValidateJsonApiDocument::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())
                    ->map(fn ($message, $field) => [
                        'title' => $e->getMessage(),
                        'detail' => $message[0],
                        'source' => ['pointer' => '/'.str_replace('.', '/', $field)],
                    ])->values(),
            ], 422, [
                'Content-Type' => 'application/vnd.api+json',
            ]);
        });
    })->create();
