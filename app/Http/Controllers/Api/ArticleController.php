<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\JsonApi\JsonApiResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['author', 'category'])
            ->sparseFields(['title', 'content', 'slug'])
            ->filterableBy(['title', 'content', 'year', 'month', 'day', 'category'])
            ->sortableBy(['title', 'content'])
            ->paginated(['sort', 'filter']);

        return ArticleResource::collection($articles);
    }

    public function show($article): JsonApiResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['author', 'category'])
            ->sparseFields(['title', 'content', 'slug'])
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function store(StoreArticleRequest $request): JsonApiResource
    {
        $article = Article::create($request->validated('attributes'));

        return ArticleResource::make($article);
    }

    public function update(StoreArticleRequest $request, Article $article): JsonApiResource
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
