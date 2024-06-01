<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateJsonApiHeaders::class)
    ->get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
Route::patch('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
