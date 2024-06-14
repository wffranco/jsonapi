<?php

namespace App\Http\Resources;

use App\JsonApi\JsonApiResource;
use App\Models\User;

/**
 * @extends JsonApiResource<User>
 */
class AuthorResource extends JsonApiResource
{
    public function allowedAttributes(): array
    {
        return ['alias', 'name', 'email'];
    }

    public function getRelationshipKeys(): array
    {
        return [];
    }
}
