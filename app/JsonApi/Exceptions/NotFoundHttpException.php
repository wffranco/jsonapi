<?php

namespace App\JsonApi\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotFoundHttpException extends Exception
{
    use ThrowException;

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'status' => '404',
                    'detail' => $this->getDetail($request),
                ],
            ],
        ], 404, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }

    protected function getDetail(Request $request): string
    {
        return str($this->getMessage())->startsWith('No query results for model')
            ? "Not found the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource."
            : $this->getMessage();
    }
}
