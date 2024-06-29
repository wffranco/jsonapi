<?php

namespace App\JsonApi;

use App\JsonApi\Mixins\BuilderMixin;
use App\JsonApi\Mixins\RequestMixin;
use App\JsonApi\Mixins\RouteMixin;
use App\JsonApi\Mixins\TestResponseMixin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Testing\TestResponse;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::mixin(new BuilderMixin());
        TestResponse::mixin(new TestResponseMixin());
        Request::mixin(new RequestMixin());
        Route::mixin(new RouteMixin());
    }
}
