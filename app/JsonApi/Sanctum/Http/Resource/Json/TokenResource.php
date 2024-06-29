<?php

namespace App\JsonApi\Sanctum\Http\Resource\Json;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class TokenResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $token = $this->resource->createToken(
            $request->device_name,
            $this->resource->permissions->pluck('name')->toArray(),
        );

        return [
            'token' => $token?->plainTextToken,
        ];
    }
}
