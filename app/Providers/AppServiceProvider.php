<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // The `after` method is not working as expected, `afterNext` is a workaround.
        Stringable::macro('afterNext', function (string $search) {
            /** @var Stringable $this */
            return $search === '' ? $this : new Stringable(
                str_contains($this->value, $search) ? Str::after($this->value, $search) : ''
            );
        });
    }
}
