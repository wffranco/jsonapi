<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
use App\Models\Article;

/**
 * @extends JsonApiResource<Article>
 */
class ArticleResource extends JsonApiResource
{
    public function allowedAttributes(): array
    {
        return ['title', 'content', 'slug'];
    }

    public function getIncludes(): array
    {
        return [
            CategoryResource::make($this->resource->category),
        ];
    }

    public function getRelationshipKeys(): array
    {
        return ['category'];
    }
}
