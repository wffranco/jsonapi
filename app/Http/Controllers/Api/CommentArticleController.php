<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\JsonApi\JsonApiResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommentArticleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['update']),
        ];
    }

    public function index(Comment $comment): JsonApiResource
    {
        return ArticleResource::make($comment->article)->identifier();
    }

    public function show(Comment $comment): JsonApiResource
    {
        return ArticleResource::make($comment->article);
    }

    public function update(Request $request, Comment $comment): JsonApiResource
    {
        $request->validate(['data.id' => 'exists:articles,slug']);

        $slug = $request->input('data.id');
        $article = Article::where('slug', $slug)->first();
        $comment->article()->associate($article)->save();

        return ArticleResource::make($article);
    }
}
