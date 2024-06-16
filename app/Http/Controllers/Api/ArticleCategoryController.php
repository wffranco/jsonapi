<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\JsonApi\JsonApiResource;
use App\Models\Article;

class ArticleCategoryController extends Controller
{
    public function index(Article $article): JsonApiResource
    {
        return CategoryResource::make($article->category)->identifier();
    }

    public function show(Article $article): JsonApiResource
    {
        return CategoryResource::make($article->category);
    }
}
