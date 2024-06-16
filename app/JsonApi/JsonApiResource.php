<?php

namespace App\JsonApi;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @property TModel|LengthAwarePaginator $resource
 */
abstract class JsonApiResource extends JsonResource
{
    protected bool $identifier = false;

    abstract public function allowedAttributes(): array;

    /**
     * @param  TModel|LengthAwarePaginator  $resources
     * @return AnonymousResourceCollection
     */
    public static function collection($resources)
    {
        $collection = parent::collection($resources);

        if (request()->filled('include')) {
            $collection->with['included'] = [];
            foreach ($resources as $resource) {
                foreach ($resource->getIncludes() as $include) {
                    if ($include->resource instanceof MissingValue) {
                        continue;
                    }
                    $collection->with['included'][] = $include;
                }
            }
        }

        $collection->with['links'] = [
            'self' => method_exists($resources, 'path')
                ? $resources->path()
                : route('api.v1.'.$resources[0]->getResourceType().'.index'),
        ];

        return $collection;
    }

    public function get(?string $key = null, $default = null): array
    {
        $data = static::$wrap
            ? [static::$wrap => $this->toArray(request())]
            : $this->toArray(request());

        return Arr::get($data, $key, $default);
    }

    /**
     * @param  TModel|LengthAwarePaginator  $resource
     */
    public static function getIdentifier($resource): array
    {
        return static::make($resource)->identifier()->get();
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
            return JsonApiDocument::make($this->resource)->get('data');
        }
        if (request()->filled('include')) {
            $this->with['included'] = [];
            foreach ($this->getIncludes() as $include) {
                if ($include->resource instanceof MissingValue) {
                    continue;
                }
                $this->with['included'][] = $include;
            }
        }

        return JsonApiDocument::make($this->resource)
            ->attributes($this->filteredAttributes())
            ->links()
            ->relationshipData($this->getRelationshipKeys())
            ->relationshipsLinks($this->getRelationshipKeys())
            ->get('data');
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->withHeaders([
            'Location' => route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource),
        ]);
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
