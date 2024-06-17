<?php

namespace App\JsonApi\Exceptions;

/**
 * @mixin \Exception
 */
trait ThrowException
{
    public static function throwIf(bool $condition, string $message = '', int $code = 0): void
    {
        if ($condition) {
            throw new static($message, $code);
        }
    }

    public static function throwUnless(bool $condition, string $message = '', int $code = 0): void
    {
        if (! $condition) {
            throw new static($message, $code);
        }
    }
}
