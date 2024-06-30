<?php

namespace App\Http\Controllers\Api\Auth\Concerns;

trait PermissionsHandler
{
    /** @param User $user */
    protected function getPermissions($user): array
    {
        return $user->permissions?->pluck('name')?->toArray() ?? [];
    }
}
