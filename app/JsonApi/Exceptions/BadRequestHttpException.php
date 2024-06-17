<?php

namespace App\JsonApi\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BadRequestHttpException extends Exception
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
                    'title' => 'Bad Request',
                    'status' => '400',
                    'detail' => $this->getMessage(),
                ],
            ],
        ], 400, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
