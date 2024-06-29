<?php

namespace App\JsonApi\Exceptions;

use App\JsonApi\Exceptions as JsonApi;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler
{
    protected static ?Closure $shouldRender = null;

    public static function render(Exceptions $exceptions)
    {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            JsonApi\HttpException::throwIf(static::shouldRenderJsonApi($request), 401,
                'This action requires authentication.', 'Unauthenticated');
            throw new HttpException(401, 'This action requires authentication.');
        })->render(function (HttpException $e, Request $request) {
            JsonApi\HttpException::throwIf(static::shouldRenderJsonApi($request), $e);
        })->render(function (UnauthorizedException $e, Request $request) {
            JsonApi\HttpException::throwIf(static::shouldRenderJsonApi($request), $e);
            throw new HttpException(401, 'You are not authorized to access this resource.');
        })->render(function (ValidationException $e, Request $request) {
            JsonApi\ValidationException::throwIf(static::shouldRenderJsonApi($request), $e);
        });
    }

    /**
     * @param  null|Closure(Request $request): bool  $callback
     */
    public static function shouldRenderJsonApiWhen(?Closure $callback)
    {
        static::$shouldRender = $callback;
    }

    public static function shouldRenderJsonApi(Request $request): bool
    {
        return static::$shouldRender
            ? (static::$shouldRender)($request)
            : $request->isJsonApi();
    }
}
