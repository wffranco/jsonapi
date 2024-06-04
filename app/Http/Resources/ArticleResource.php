<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
use App\Models\Article;
use Illuminate\Http\Request;

/**
 * @extends JsonApiResource<Article>
 */
class ArticleResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->resource->title,
            'content' => $this->resource->content,
            'slug' => $this->resource->slug,
        ];
    }
}
