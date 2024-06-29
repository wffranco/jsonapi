<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\JsonApi\Http\Resources\Json\JsonApiResource;
use App\JsonApi\Sanctum\Contracts\Authorize;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommentAuthorController extends Controller implements HasMiddleware
{
    use Authorize;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Comment $comment): JsonApiResource
    {
        return AuthorResource::make($comment->author)->identifier();
    }

    public function show(Comment $comment): JsonApiResource
    {
        return AuthorResource::make($comment->author);
    }

    public function update(Request $request, Comment $comment): JsonApiResource
    {
        $request->validate(['data.id' => 'exists:users,id']);

        $comment->user_id = $request->input('data.id');
        $comment->save();

        return AuthorResource::make($comment->refresh()->author);
    }
}
