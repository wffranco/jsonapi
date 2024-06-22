<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
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
        return [];
    }
}
