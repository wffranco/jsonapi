<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as Exception;

class ValidationException extends Exception
{
    public static function throwIf($condition, ?array $messages = null)
    {
        if ($condition) {
            throw parent::withMessages($messages);
        }
    }

    public static function throwUnless($condition, ?array $messages = null)
    {
        if (! $condition) {
            throw parent::withMessages($messages);
        }
    }

    public function render(Request $request)
    {
        if ($request->routeIs('api.v1.login')) {
            return;
        }

        return response()->json([
            'errors' => collect($this->errors())
                ->map(fn ($message, $field) => [
                    'title' => $this->getMessage(),
                    'detail' => $message[0],
                    'source' => ['pointer' => '/'.str_replace('.', '/', $field)],
                ])->values(),
        ], 422, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
