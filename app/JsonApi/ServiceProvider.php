<?php

namespace App\JsonApi;

use Illuminate\Database\Eloquent\Builder;
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
        Builder::mixin(new JsonApiEloquentBuilderMixin());
        TestResponse::mixin(new JsonApiTestResponseMixin());
    }
}
