<?php

namespace App\JsonApi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\JsonResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept') !== 'application/vnd.api+json') {
            throw new HttpException(406, 'Not Acceptable');
        }

        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            if ($request->header('Content-Type') !== 'application/vnd.api+json') {
                throw new HttpException(415, 'Unsupported Media Type');
            }
        }

        return $next($request)->withHeaders([
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
