<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\JsonApi\JsonApiResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CommentController extends Controller
{
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
    public function store(Request $request): JsonApiResource
    {
        $comment = Comment::create($request->validated('attributes'));

        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment): JsonApiResource
    {
        $comment->update($request->validated('attributes'));

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
}
