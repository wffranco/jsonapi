<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @extends JsonApiResource<Category>
 */
class CategoryResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
        ];
    }
}
