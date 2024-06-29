<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as Exception;

class ValidationException extends Exception
{
    /**
     * @param  bool  $condition
     * @param  null|array|Exception  $messages
     */
    public static function throwIf($condition, $messages = null)
    {
        if ($condition) {
            $messages instanceof Exception
                ? throw new ValidationException($messages->validator, $messages->response, $messages->errorBag)
                : throw static::withMessages($messages ?? []);
        }
    }

    /**
     * @param  bool  $condition
     * @param  null|array|Exception  $messages
     */
    public static function throwUnless($condition, $messages = null)
    {
        if (! $condition) {
            $messages instanceof Exception
                ? throw new ValidationException($messages->validator, $messages->response, $messages->errorBag)
                : throw static::withMessages($messages ?? []);
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
