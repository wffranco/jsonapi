<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateJsonApiHeaders::class)
    ->get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::apiResource('categories', CategoryController::class)
    ->except('store', 'update', 'destroy');

Route::apiResource('articles', ArticleController::class);
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('{article}/relationships/author', fn () => 'TODO')->name('relationships.author');
    Route::get('{article}/author', fn () => 'TODO')->name('author');

    Route::get('{article}/relationships/category', fn () => 'TODO')->name('relationships.category');
    Route::get('{article}/category', fn () => 'TODO')->name('category');
});
