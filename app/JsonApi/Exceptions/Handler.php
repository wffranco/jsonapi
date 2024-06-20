<?php

namespace App\JsonApi\Exceptions;

use App\JsonApi\Exceptions as JsonApi;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    public static function render(Exceptions $exceptions)
    {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->isJsonApi()) {
                return (new JsonApi\AuthenticationException($e->getMessage(), $e->getCode(), $e))->render($request);
            }
        });
        $exceptions->render(function (BadRequestHttpException $e, Request $request) {
            if ($request->isJsonApi()) {
                return (new JsonApi\BadRequestHttpException($e->getMessage(), $e->getCode(), $e))->render($request);
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->isJsonApi()) {
                return (new JsonApi\NotFoundHttpException($e->getMessage(), $e->getCode(), $e))->render($request);
            }
        });
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->isJsonApi()) {
                return (new JsonApi\UnauthorizedException($e->getMessage(), $e->getCode(), $e))->render($request);
            }

            return response()->json(['message' => 'Unauthorized.'], 401);
        });
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->isJsonApi()) {
                return (new JsonApi\ValidationException($e->validator))->render($request);
            }
        });
    }
}
