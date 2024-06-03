<?php

namespace App\JsonApi;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder when(bool $condition, \Closure(Builder) $callback, \Closure(Builder) $default = null)
 *
 * @mixin Builder
 */
class JsonApiEloquentBuilderMixin
{
    public function paginated()
    {
        return function (array|string|null $appends = null): LengthAwarePaginator {
            $keys = array_filter(array_merge(['page.size'], (array) $appends));

            return $this->paginate(
                perPage: request('page.size', 15),
                pageName: 'page[number]',
                page: request('page.number', 1),
            )->appends(request()->only($keys));
        };
    }

    public function sortableBy()
    {
        return function (array $allowed = []): Builder {
            return $this->when(request()->filled('sort'), function (Builder $query) use ($allowed) {
                foreach (explode(',', request()->get('sort')) as $sort) {
                    $field = ltrim($sort, '-');
                    abort_unless(in_array($field, $allowed), 400, "Invalid sort field: sort.{$field}");
                    $direction = $sort[0] === '-' ? 'desc' : 'asc';
                    $query->orderBy($field, $direction);
                }
            });
        };
    }
}
