<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\JsonApi\JsonApiAuthorize;
use App\JsonApi\JsonApiResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommentController extends Controller implements HasMiddleware
{
    use JsonApiAuthorize;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::paginated([]);

        return CommentResource::collection($comments);
    }

    /**
     * Display the specified resource.
     */
    public function show($comment): JsonApiResource
    {
        $comment = Comment::findOrFail($comment);

        return CommentResource::make($comment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request): JsonApiResource
    {
        $comment = Comment::create($this->transform($request));

        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCommentRequest $request, Comment $comment): JsonApiResource
    {
        $this->authorize('update', $comment);
        $comment->update($this->transform($request));

        return CommentResource::make($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): Response
    {
        $comment->delete();

        return response()->noContent();
    }

    protected function transform(Request $request): array
    {
        $attributes = $request->getAttributes();

        $slug = $request->getRelationshipId('article');
        if ($slug && $article = Article::where('slug', $slug)->first()) {
            $attributes['article_id'] = $article->id;
        }

        if ($user_id = $request->getRelationshipId('author')) {
            $attributes['user_id'] = $user_id;
        }

        return $attributes;
    }
}
