<?php

namespace App\JsonApi\Sanctum\Testing;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Laravel\Sanctum\Sanctum;

/**
 * @mixin \Tests\TestCase
 */
trait CanAuthenticate
{
    public function actingAs(?UserContract $user = null, $abilities = null): static
    {
        Sanctum::actingAs(
            $user ?? User::factory()->createOne(),
            $abilities ?? [],
        );

        return $this;
    }
}
