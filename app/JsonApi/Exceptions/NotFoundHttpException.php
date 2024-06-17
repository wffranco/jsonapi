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
        $id = $request->input('data.id');
        $type = $request->input('data.type');
        $detail = $id && $type
            ? "Not found the id '{$id}' in the '{$type}' resource."
            : $this->getMessage();

        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'status' => '404',
                    'detail' => $detail,
                ],
            ],
        ], 404, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
