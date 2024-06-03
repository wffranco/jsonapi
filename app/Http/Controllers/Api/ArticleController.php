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
        $allowedFilters = ['title', 'content', 'year', 'month', 'day'];
        $articles = Article::query()
            ->when(request()->filled('filter'), function ($query) use ($allowedFilters) {
                foreach (request('filter') as $field => $value) {
                    abort_unless(in_array($field, $allowedFilters), 400, "Filter not allowed: filter.{$field}");
                    if ($field === 'year') {
                        $query->whereYear('created_at', $value);
                    } elseif ($field === 'month') {
                        $query->whereMonth('created_at', $value);
                    } elseif ($field === 'day') {
                        $query->whereDay('created_at', $value);
                    } else {
                        $query->where($field, 'like', '%'.$value.'%');
                    }
                }
            })
            ->sortableBy(['title', 'content'])
            ->paginated('sort');

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
