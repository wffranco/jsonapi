<?php

namespace App\JsonApi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
        Builder::macro('sortableBy', function (array $allowed = []): Builder {
            /** @var Builder $this */
            return $this->when(request()->filled('sort'), function (Builder $query) use ($allowed) {
                foreach (explode(',', request()->get('sort')) as $sort) {
                    $field = ltrim($sort, '-');
                    abort_unless(in_array($field, $allowed), 400, "Invalid sort field: sort.{$field}");
                    $direction = $sort[0] === '-' ? 'desc' : 'asc';
                    $query->orderBy($field, $direction);
                }
            });
        });
    }
}
