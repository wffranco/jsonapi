<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\JsonApi\JsonApiResource;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleAuthorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['update']),
        ];
    }

    public function index(Article $article): JsonApiResource
    {
        return AuthorResource::make($article->author)->identifier();
    }

    public function show(Article $article): JsonApiResource
    {
        return AuthorResource::make($article->author);
    }

    public function update(Request $request, Article $article): JsonApiResource
    {
        $request->validate(['data.id' => 'exists:users,id']);

        $author = User::find($request->input('data.id'));
        $article->update(['user_id' => $author->id]);

        return AuthorResource::make($author);
    }
}
