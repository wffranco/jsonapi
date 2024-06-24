<?php

use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateJsonApiHeaders::class)
    ->get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class,
])->prefix('auth')->name('auth.')->group(function () {
    Route::post('login', LoginController::class)->name('login');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('register', RegisterController::class)->name('register');
});

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::apiResource('categories', CategoryController::class)
    ->except('store', 'update', 'destroy');

Route::apiResource('articles', ArticleController::class);
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('{article}/relationships/author', [ArticleAuthorController::class, 'index'])->name('relationships.author');
    Route::patch('{article}/relationships/author', [ArticleAuthorController::class, 'update'])->name('relationships.author');
    Route::get('{article}/author', [ArticleAuthorController::class, 'show'])->name('author');

    Route::get('{article}/relationships/category', [ArticleCategoryController::class, 'index'])->name('relationships.category');
    Route::patch('{article}/relationships/category', [ArticleCategoryController::class, 'update'])->name('relationships.category');
    Route::get('{article}/category', [ArticleCategoryController::class, 'show'])->name('category');
});

Route::apiResource('comments', CommentController::class)
    ->only('index', 'show', 'store', 'update');
