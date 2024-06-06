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

    abstract public function allowedAttributes(): array;

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
        return JsonApiDocument::make($this->resource)
            ->attributes($this->filteredAttributes())
            ->links([
                'self' => $this->selfLink(),
            ])
            ->get('data');
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->withHeaders(['Location' => $this->selfLink()]);
    }

    protected function filteredAttributes(): array
    {
        $type = $this->resource->getResourceType();
        $fields = request()->filled("fields.{$type}") ? request("fields.{$type}") : null;

        return $fields
            ? array_intersect(explode(',', $fields), $this->allowedAttributes())
            : $this->allowedAttributes();
    }

    protected function selfLink(): string
    {
        return $this->selfLink ??= route('api.v1.'.$this->resource->getResourceType().'.show', $this->resource);
    }
}
