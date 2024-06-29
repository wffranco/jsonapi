<?php

namespace App\Http\Resources;

use App\JsonApi\Http\Resources\Json\JsonApiResource;
use App\Models\Comment;

/**
 * @extends JsonApiResource<Comment>
 */
class CommentResource extends JsonApiResource
{
    public function allowedAttributes(): array
    {
        return ['body'];
    }

    public function getRelationshipKeys(): array
    {
        return ['article', 'author'];
    }
}
