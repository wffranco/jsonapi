<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));

Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
