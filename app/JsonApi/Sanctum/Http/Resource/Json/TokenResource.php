<?php

namespace App\JsonApi\Sanctum\Http\Resource\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property NewAccessToken $resource
 */
class TokenResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource->plainTextToken,
        ];
    }
}
