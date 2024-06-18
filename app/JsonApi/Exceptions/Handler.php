<?php

namespace App\JsonApi\Exceptions;

use App\JsonApi\Exceptions as JsonApi;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    protected function __construct(
        protected Exceptions $exceptions,
    ) {}

    public static function with(Exceptions $exceptions): static
    {
        return new static($exceptions);
    }

    public function setRenderJson()
    {
        $this->exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/v1/*') || $request->expectsJson());

        return $this;
    }

    public function renderExceptions()
    {
        $this->exceptions->render(function (AuthenticationException $e, Request $request) {
            return (new JsonApi\AuthenticationException($e->getMessage(), $e->getCode(), $e))->render($request);
        });
        $this->exceptions->render(function (BadRequestHttpException $e, Request $request) {
            return (new JsonApi\BadRequestHttpException($e->getMessage(), $e->getCode(), $e))->render($request);
        });
        $this->exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return (new JsonApi\NotFoundHttpException($e->getMessage(), $e->getCode(), $e))->render($request);
        });
        $this->exceptions->render(function (ValidationException $e, Request $request) {
            return (new JsonApi\ValidationException($e->validator))->render($request);
        });

        return $this;
    }
}
