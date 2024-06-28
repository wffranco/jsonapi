<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\JsonApi\JsonApiAuthorize;
use App\JsonApi\JsonApiResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleController extends Controller implements HasMiddleware
{
    use JsonApiAuthorize;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['author', 'category', 'comments'])
            ->sparseFields(['title', 'content', 'slug'])
            ->filterableBy(['title', 'content', 'year', 'month', 'day', 'category'])
            ->sortableBy(['title', 'content'])
            ->paginated(['sort', 'filter']);

        return ArticleResource::collection($articles);
    }

    public function show($article): JsonApiResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['author', 'category', 'comments'])
            ->sparseFields(['title', 'content', 'slug'])
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function store(StoreArticleRequest $request): JsonApiResource
    {
        $this->authorize('create', Article::class);
        $article = Article::create($this->transform($request));

        return ArticleResource::make($article);
    }

    public function update(StoreArticleRequest $request, Article $article): JsonApiResource
    {
        $this->authorize('update', $article);
        $article->update($this->transform($request));

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $this->authorize('delete', $article);
        $article->delete();

        return response()->noContent();
    }

    protected function transform(Request $request): array
    {
        $attributes = $request->getAttributes();

        $slug = $request->getRelationshipId('category');
        if ($slug && $category = Category::where('slug', $slug)->first()) {
            $attributes['category_id'] = $category->id;
        }

        if ($author_id = $request->getRelationshipId('author')) {
            $attributes['user_id'] = $author_id;
        }

        return $attributes;
    }
}
