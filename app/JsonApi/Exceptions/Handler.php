<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
