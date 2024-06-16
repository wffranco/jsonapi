<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\JsonApi\JsonApiResource;
use App\Models\Article;

class ArticleAuthorController extends Controller
{
    public function index(Article $article): JsonApiResource
    {
        return AuthorResource::make($article->author)->identifier();
    }

    public function show(Article $article): JsonApiResource
    {
        return AuthorResource::make($article->author);
    }
}
