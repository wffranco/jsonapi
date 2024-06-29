<?php

namespace App\JsonApi\Exceptions;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @mixin HttpException
 */
trait ThrowException
{
    public static function throwIf(bool $condition, int|HttpExceptionInterface|RuntimeException $code = 0, string $message = '', string $title = ''): void
    {
        if ($condition) {
            throw new static($code, $message, $title);
        }
    }

    public static function throwUnless(bool $condition, int|HttpExceptionInterface|RuntimeException $code = 0, string $message = '', string $title = ''): void
    {
        if (! $condition) {
            throw new static($code, $message, $title);
        }
    }
}
