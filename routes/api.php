<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateJsonApiHeaders::class)
    ->get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::apiResource('categories', CategoryController::class)
    ->except('store', 'update', 'destroy');

Route::apiResource('articles', ArticleController::class);
Route::get('articles/{article}/relationships/category', fn () => 'TODO')
    ->name('articles.relationships.category');
Route::get('articles/{article}/category', fn () => 'TODO')
    ->name('articles.category');
