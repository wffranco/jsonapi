<?php

namespace App\JsonApi;

use Closure;
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
        return function (string $uri, Closure|string $controller, array $only = ['index', 'show', 'update'], array $except = []) {
            $methods = collect(array_diff($only, $except));
            // $uri has the format: `<model>/<relationship>`. the key is the model name in singular form.
            [$model, $relationship] = explode('/', $uri);
            $key = '{'.Str::singular($model).'}';
            $route = Route::prefix("$model/$key")->name($model.'.');
            $callable = is_string($controller) ? null : $controller;
            ! $callable && $route = $route->controller($controller);
            $route->group(function () use ($callable, $relationship, $methods) {
                $methods->contains('index') && Route::get('relationships/'.$relationship, $callable ?? 'index')->name('relationships.'.$relationship);
                $methods->contains('update') && Route::patch('relationships/'.$relationship, $callable ?? 'update')->name('relationships.'.$relationship);
                $methods->contains('show') && Route::get($relationship, $callable ?? 'show')->name($relationship);
            });
        };
    }
}
