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
            ->when($request->filled('sort'), function ($query) use ($request) {
                $allowedFields = ['title', 'content'];
                foreach (explode(',', $request->get('sort')) as $sort) {
                    $field = ltrim($sort, '-');
                    abort_unless(in_array($field, $allowedFields), 400, "Invalid sort field: sort.{$field}");
                    $direction = $sort[0] === '-' ? 'desc' : 'asc';
                    $query->orderBy($field, $direction);
                }
            })
            ->get();

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
