<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(): ArticleCollection
    {
        return ArticleCollection::make(Article::all());
    }

    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }
}
