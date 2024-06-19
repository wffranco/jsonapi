<?php

namespace App\JsonApi\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    use ThrowException;

    public function render(Request $request): Response
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthorized',
                    'detail' => $this->message ?: 'You are not authorized to access this resource.',
                    'status' => '401',
                ],
            ],
        ], 401, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
