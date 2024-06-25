<?php

namespace App\JsonApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * @mixin Request
 */
class JsonApiRouteMixin
{
    public function apiRelationshipResource()
    {
        return function (string $uri, string $controller, array $only = ['index', 'show', 'update'], array $except = []) {
            $methods = collect(array_diff($only, $except));
            // $uri has the format: `<model>/<relationship>`. the key is the model name in singular form.
            [$model, $relationship] = explode('/', $uri);
            $key = '{'.Str::singular($model).'}';
            Route::controller($controller)->prefix("$model/$key")->name($model.'.')->group(function () use ($relationship, $methods) {
                $methods->contains('index') && Route::get('relationships/'.$relationship, 'index')->name('relationships.'.$relationship);
                $methods->contains('update') && Route::patch('relationships/'.$relationship, 'update')->name('relationships.'.$relationship);
                $methods->contains('show') && Route::get($relationship, 'show')->name($relationship);
            });
        };
    }
}
