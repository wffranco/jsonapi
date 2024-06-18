<?php

namespace App\JsonApi\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends Exception
{
    use ThrowException;

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): Response
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthenticated',
                    'detail' => 'This action requires authentication.',
                    'status' => '401',
                ],
            ],
        ], 401, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
