<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonApiDocument
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            $request->validate([
                'data' => ['required', 'array'],
                'data.type' => [
                    'required_without:data.0.type',
                    'string',
                ],
                'data.attributes' => [
                    Rule::requiredIf(! Str::contains($request->url(), 'relationships') && $request->isNotFilled('data.0.type')),
                    'array',
                ],
                'data.id' => [
                    Rule::requiredIf($request->isMethod('PATCH') && $request->isNotFilled('data.0.type')),
                    'string',
                ],
                'data.*.type' => [
                    Rule::requiredIf($request->isMethod('PATCH') && $request->isNotFilled('data.type')),
                    'string',
                ],
                'data.*.id' => ['required_with:data.*.type', 'string'],
            ]);
        }

        return $next($request);
    }
}
