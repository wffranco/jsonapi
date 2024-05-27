<?php

use Illuminate\Support\Facades\Route;

Route::get('info', fn () => response()->json(['name' => 'Laravel JSON:API', 'version' => app()->version()]));
