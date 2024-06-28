<?php

namespace App\JsonApi\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class NotFoundHttpException extends Exception
{
    use ThrowException;

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        $type = $request->hasJsonApiContent('data.type') ? $request->input('data.type') : null;
        $id = $request->hasJsonApiContent('data.id') ? $request->input('data.id') : null;
        if (! $type && ! $id) {
            $path = Str::contains($request->path(), 'api/v1/') ? Str::after($request->path(), 'api/v1/') : '';
            $type = strtok($path, '/');
            $id = strtok('/');
        }
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
