<?php

namespace App\JsonApi;

use App\JsonApi\Exceptions\BadRequestHttpException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder when(bool $condition, \Closure(Builder) $callback, \Closure(Builder) $default = null)
 *
 * @mixin Builder
 */
class JsonApiEloquentBuilderMixin
{
    public function allowedIncludes()
    {
        return function (array $allowed = []): Builder {
            return $this->when(request()->filled('include'), function (Builder $query) use ($allowed) {
                $includes = explode(',', request('include'));
                $notAllowed = collect($includes)->diff($allowed);
                BadRequestHttpException::throwIf(! $notAllowed->isEmpty(), "Includes not allowed in '{$query->getResourceType()}' resource: {$notAllowed->implode(', ')}.");

                return $query->with($includes);
            });
        };
    }

    public function filterableBy()
    {
        return function (array $allowed = []): Builder {
            return $this->when(request()->filled('filter'), function (Builder $query) use ($allowed) {
                foreach (request('filter') as $field => $value) {
                    BadRequestHttpException::throwIf(! in_array($field, $allowed), "Filter not allowed in '{$query->getResourceType()}' resource: {$field}.");
                    $query->hasNamedScope($field)
                        ? $query->{$field}($value)
                        : $query->where($field, 'like', '%'.$value.'%');
                }
            });
        };
    }

    public function getResourceType()
    {
        return function (): string {
            $model = $this->getModel();
            if (method_exists($model, 'getResourceType')) {
                return $model->getResourceType();
            }

            return $model?->resourceType ?? $model->getTable();
        };
    }

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
                $sortFields = explode(',', request('sort'));
                $notAllowed = collect($sortFields)->map(fn ($sort) => ltrim($sort, '-'))->diff($allowed);
                BadRequestHttpException::throwIf(! $notAllowed->isEmpty(), "Invalid sort fields in '{$query->getResourceType()}' resource: {$notAllowed->implode(', ')}.");
                foreach ($sortFields as $sort) {
                    $field = ltrim($sort, '-');
                    $direction = $sort[0] === '-' ? 'desc' : 'asc';
                    $query->orderBy($field, $direction);
                }
            });
        };
    }

    public function sparseFields()
    {
        return function (array $allowed = []): Builder {
            return $this->when(request()->filled('fields'), function (Builder $query) use ($allowed) {
                $type = $query->getResourceType();
                $group = request("fields.{$type}");
                if (empty($group)) {
                    return;
                }
                $fields = explode(',', $group);
                $routeKeyName = $query->getModel()->getRouteKeyName();
                if (! in_array($routeKeyName, $fields)) {
                    $fields[] = $routeKeyName;
                }

                $notAllowed = collect($fields)->diff($allowed)->map(fn ($field) => "fields.{$type}.{$field}");
                BadRequestHttpException::throwIf(! $notAllowed->isEmpty(), "Invalid fields requested in '{$query->getResourceType()}' resource: {$notAllowed->implode(', ')}.");

                $query->addSelect($fields);
            });
        };
    }
}
