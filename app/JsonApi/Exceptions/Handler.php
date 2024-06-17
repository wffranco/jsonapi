<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        $this->exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $id = $request->input('data.id');
            $type = $request->input('data.type');

            return response()->json([
                'errors' => [
                    [
                        'title' => 'Not Found',
                        'status' => '404',
                        'detail' => "Not found the id '{$id}' in the '{$type}' resource.",
                    ],
                ],
            ], 404, [
                'Content-Type' => 'application/vnd.api+json',
            ]);
        });
        $this->exceptions->render(function (ValidationException $e) {
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

        return $this;
    }
}
