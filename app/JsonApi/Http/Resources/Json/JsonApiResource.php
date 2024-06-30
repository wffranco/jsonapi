<?php

namespace App\JsonApi\Http\Resources\Json;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @template TModel of Model
 *
 * @property TModel|LengthAwarePaginator $resource
 */
abstract class JsonApiResource extends JsonResource
{
    protected bool $identifier = false;

    abstract public function allowedAttributes(): array;

    /**
     * @param  Collection<TModel>|LengthAwarePaginator  $resources
     */
    public static function collection($resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        if (request()->filled('include')) {
            foreach ($collection->resource as $resource) {
                foreach ($resource->getIncludes() as $include) {
                    if ($include->resource instanceof Collection) {
                        $include->resource->each(fn ($r) => $collection->with['included'][] = $r);
                    } elseif (! ($include->resource instanceof MissingValue)) {
                        $collection->with['included'][] = $include;
                    }
                }
            }
        }

        $collection->with['links'] = ['self' => url(request()->path())];

        return $collection;
    }

    public static function collectionIdentifiers(Collection|LengthAwarePaginator $resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        foreach ($collection->getIterator() as $resource) {
            $resource->identifier();
        }

        return $collection;
    }

    public function get(?string $key = null, $default = null): array
    {
        $data = static::$wrap
            ? [static::$wrap => $this->toArray(request())]
            : $this->toArray(request());

        return Arr::get($data, $key, $default);
    }

    public static function getCollectionResources(Collection|LengthAwarePaginator $resources): array
    {
        return static::collection($resources)->toArray(request());
    }

    public static function getIdentifier(Model|LengthAwarePaginator $resource): array
    {
        return static::make($resource)->identifier()->get();
    }

    /**
     * @param  Collection<TModel>|LengthAwarePaginator  $resources
     */
    public static function getIdentifiers(Collection|LengthAwarePaginator $resource): array
    {
        return $resource->map(
            fn (Model $model) => static::getIdentifier($model)[static::$wrap]
        )->all();
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipKeys(): ?array
    {
        return null;
    }

    /**
     * @param  TModel|LengthAwarePaginator  $resource
     */
    public static function getResource($resource): array
    {
        return static::make($resource)->get();
    }

    public function identifier(bool $identifier = true): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->identifier) {
            return Document::make($this->resource)->get('data');
        }
        if (request()->filled('include')) {
            foreach ($this->getIncludes() as $include) {
                if ($include->resource instanceof Collection) {
                    $include->resource->each(fn ($r) => $this->with['included'][] = $r);
                } elseif (! ($include->resource instanceof MissingValue)) {
                    $this->with['included'][] = $include;
                }
            }
        }

        return Document::make($this->resource)
            ->attributes($this->filteredAttributes())
            ->links()
            ->relationshipData($this->getRelationshipKeys())
            ->relationshipsLinks($this->getRelationshipKeys())
            ->get('data');
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        if ($response->status() === 201) {
            $response->withHeaders([
                'Location' => route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource),
            ]);
        }
    }

    protected function filteredAttributes(): array
    {
        $type = $this->resource->getResourceType();
        $fields = request()->filled("fields.{$type}") ? request("fields.{$type}") : null;

        return $fields
            ? array_intersect(explode(',', $fields), $this->allowedAttributes())
            : $this->allowedAttributes();
    }
}
