<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\JsonApi\JsonApiAuthorize;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleCommentsController extends Controller implements HasMiddleware
{
    use JsonApiAuthorize;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['update']),
        ];
    }

    public function index(Article $article): AnonymousResourceCollection
    {
        return CommentResource::collectionIdentifiers($article->comments);
    }

    public function show($article): AnonymousResourceCollection
    {
        $article = Article::where('slug', $article)->firstOrFail();

        return CommentResource::collection($article->comments);
    }

    public function update(Request $request, Article $article): AnonymousResourceCollection
    {
        $request->validate([
            'data.*.type' => ['required', 'in:comments'],
            'data.*.id' => ['required', 'distinct', 'exists:comments,id'],
        ]);
        $comments = Comment::find($request->input('data.*.id'));
        $this->authorize('update', $comments);

        $article->comments()->saveMany($comments);

        return CommentResource::collection($comments);
    }
}
