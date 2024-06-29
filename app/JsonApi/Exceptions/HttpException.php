<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpException extends BaseHttpException
{
    use ThrowException;

    public function __construct(int|HttpExceptionInterface|RuntimeException $code, string $message = '', protected string $title = '')
    {
        if ($code instanceof HttpExceptionInterface) {
            parent::__construct($code->getStatusCode(), $code->getMessage());
        } elseif ($code instanceof RuntimeException) {
            parent::__construct($code->getCode(), $code->getMessage());
        } else {
            parent::__construct($code, $message);
        }
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'errors' => [
                [
                    'title' => $this->title ?: Response::$statusTexts[$this->getStatusCode()],
                    'status' => (string) $this->getStatusCode(),
                    'detail' => $this->getDetail($request) ?: $this->getMessage(),
                ],
            ],
        ], $this->getStatusCode(), [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }

    protected function getDetail(Request $request): string
    {
        return match ($this->getStatusCode()) {
            404 => ! str($this->getMessage())->startsWith('No query results for model') ? '' :
                "Not found the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource.",
            default => '',
        };
    }
}
