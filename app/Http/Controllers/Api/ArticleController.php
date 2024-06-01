<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

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

    public function store(Request $request): ArticleResource
    {
        $request->validate([
            'data.attributes.title' => ['required'],
            'data.attributes.content' => ['required'],
            'data.attributes.slug' => ['required'],
        ]);
        $article = Article::create([
            'title' => $request->input('data.attributes.title'),
            'content' => $request->input('data.attributes.content'),
            'slug' => $request->input('data.attributes.slug'),
        ]);

        return ArticleResource::make($article);
    }

    public function update(Request $request, Article $article): ArticleResource
    {
        $request->validate([
            'data.attributes.title' => 'required',
            'data.attributes.content' => 'required',
            'data.attributes.slug' => 'required',
        ]);
        $article->update([
            'title' => $request->input('data.attributes.title'),
            'content' => $request->input('data.attributes.content'),
            'slug' => $request->input('data.attributes.slug'),
        ]);

        return ArticleResource::make($article);
    }
}
