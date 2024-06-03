<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Article $resource
 */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fields = $request->filled('fields.articles') ? explode(',', data_get($request, 'fields.articles')) : null;

        return [
            'type' => 'articles',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => array_filter([
                'title' => $this->resource->title,
                'content' => $this->resource->content,
                'slug' => $this->resource->slug,
            ], fn ($key) => ! $fields ? true : in_array($key, $fields), ARRAY_FILTER_USE_KEY),
            'links' => [
                'self' => route('api.v1.articles.show', $this->resource),
            ],
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->withHeaders([
            'Location' => route('api.v1.articles.show', $this->resource),
        ]);
    }
}
