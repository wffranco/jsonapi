<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
use App\Models\Category;

/**
 * @extends JsonApiResource<Category>
 */
class CategoryResource extends JsonApiResource
{
    public function allowedAttributes(): array
    {
        return ['name', 'slug'];
    }

    public function getRelationshipKeys(): array
    {
        return [];
    }
}
