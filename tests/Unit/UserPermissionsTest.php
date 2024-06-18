<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserPermissionsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_assign_permission_to_an_user()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();
        $user->givePermissionsTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }

    public function test_cannot_assign_same_permission_to_an_user_twice()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();
        $user->givePermissionsTo($permission);
        $user->givePermissionsTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
}
