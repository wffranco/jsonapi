<?php

use App\JsonApi\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

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
        $middleware->redirectUsersTo(function (Request $request) {
            throw_if($request->is('api/v1/*') || $request->expectsJson(), UnauthorizedException::class);
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Handler::render($exceptions);
    })->create();
