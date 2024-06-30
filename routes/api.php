<?php

use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleCommentsController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentArticleController;
use App\Http\Controllers\Api\CommentAuthorController;
use App\Http\Controllers\Api\CommentController;
use App\JsonApi\Http\Middleware\ValidateDocument;
use App\JsonApi\Http\Middleware\ValidateHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateHeaders::class)
    ->get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::withoutMiddleware([
    ValidateDocument::class,
    ValidateHeaders::class,
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
Route::apiRelationshipResource('articles/author', ArticleAuthorController::class);
Route::apiRelationshipResource('articles/category', ArticleCategoryController::class);
Route::apiRelationshipResource('articles/comments', ArticleCommentsController::class);

Route::apiResource('comments', CommentController::class);
Route::apiRelationshipResource('comments/article', CommentArticleController::class);
Route::apiRelationshipResource('comments/author', CommentAuthorController::class);
