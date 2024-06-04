<?php

namespace App\JsonApi;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @property TModel|LengthAwarePaginator $resource
 */
abstract class JsonApiResource extends JsonResource
{
    private string $selfLink;

    abstract public function toAttributes(Request $request): array;

    /**
     * @param  TModel|LengthAwarePaginator  $resources
     * @return AnonymousResourceCollection
     */
    public static function collection($resources)
    {
        $collection = parent::collection($resources);
        $collection->with['links'] = [
            'self' => method_exists($resources, 'path')
                ? $resources->path()
                : route('api.v1.'.$resources[0]->getResourceType().'.index'),
        ];

        return $collection;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->resource->getResourceType(),
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => $this->filteredAttributes($request),
            'links' => [
                'self' => $this->selfLink(),
            ],
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->withHeaders(['Location' => $this->selfLink()]);
    }

    protected function filteredAttributes(Request $request): array
    {
        $type = $this->resource->getResourceType();
        $fields = request()->filled("fields.{$type}") ? explode(',', request("fields.{$type}")) : null;
        $attributes = $this?->toAttributes($request);

        return $fields
            ? array_filter($attributes, fn ($key) => in_array($key, $fields), ARRAY_FILTER_USE_KEY)
            : $attributes;
    }

    protected function selfLink(): string
    {
        return $this->selfLink ??= route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource);
    }
}
