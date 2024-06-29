<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\JsonApi\Http\Resources\Json\JsonApiResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['update']),
        ];
    }

    public function index(Article $article): JsonApiResource
    {
        return CategoryResource::make($article->category)->identifier();
    }

    public function show(Article $article): JsonApiResource
    {
        return CategoryResource::make($article->category);
    }

    public function update(Request $request, Article $article): JsonApiResource
    {
        $request->validate(['data.id' => 'exists:categories,slug']);

        $slug = $request->input('data.id');
        $category = Category::where('slug', $slug)->first();
        $category->articles()->save($article);

        return CategoryResource::make($category);
    }
}
