<?php

namespace App\Http\Resources;

use App\JsonApi\Http\Resources\Json\JsonApiResource;
use App\Models\Article;

/**
 * @extends JsonApiResource<Article>
 */
class ArticleResource extends JsonApiResource
{
    public function allowedAttributes(): array
    {
        return ['title', 'content', 'slug', 'created_at', 'updated_at'];
    }

    public function getIncludes(): array
    {
        return [
            AuthorResource::make($this->whenLoaded('author')),
            CategoryResource::make($this->whenLoaded('category')),
            CommentResource::collection($this->whenLoaded('comments')),
        ];
    }

    public function getRelationshipKeys(): array
    {
        return ['author', 'category', 'comments'];
    }
}
