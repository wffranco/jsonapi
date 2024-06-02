<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index(Request $request): ArticleCollection
    {
        $articles = Article::query()
            ->sortableBy(['title', 'content'])
            ->paginate(
                perPage: data_get($request, 'page.size', $size ?? 15),
                pageName: 'page[number]',
                page: data_get($request, 'page.number', $page ?? 1),
            )
            ->appends($request->only('page.size'))
            ->appends($request->only('sort'));

        return ArticleCollection::make($articles);
    }

    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    public function store(StoreArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated('attributes'));

        return ArticleResource::make($article);
    }

    public function update(StoreArticleRequest $request, Article $article): ArticleResource
    {
        $article->update($request->validated('attributes'));

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
