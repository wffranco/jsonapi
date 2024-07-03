<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\JsonApi\Http\Resources\Json\JsonApiResource;
use App\JsonApi\Sanctum\Contracts\Authorize;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    use Authorize;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request): JsonApiResource
    {
        return AuthorResource::make($request->user());
    }
}
