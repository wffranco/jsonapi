<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use Auth, RefreshDatabase;

    public function test_can_login()
    {
        $user = User::factory()->create();

        $response = $this->login(user: $user);

        $token = $response->json('token');
        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));
    }

    public function test_cannot_login_if_already_authenticated()
    {
        $user = User::factory()->create();
        $this->logged($user)->login(user: $user)
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthorized.']);

        $this->assertCount(1, $user->fresh()->tokens);
    }

    public function test_user_permissions_are_assigned_as_abilities_to_the_token()
    {
        $user = User::factory()->create();
        $permissions = Permission::factory()->count(3)->createMany();
        // Assign only two permissions to the user
        $user->givePermissionsTo([$permissions[0], $permissions[1]]);

        $this->assertCount(2, $user->fresh()->permissions);

        $token = $this->token(user: $user);
        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->can($permissions[0]->name));
        $this->assertTrue($dbToken->can($permissions[1]->name));
        $this->assertFalse($dbToken->can($permissions[2]->name));
    }

    public function test_password_is_required()
    {
        $this->login(['password' => null])
            ->assertJsonValidationErrors(['password' => 'required']);
    }

    public function test_password_must_be_valid()
    {
        $this->login(['password' => 'wrong-password'])
            ->assertJsonValidationErrors('email');
    }

    public function test_user_must_be_registered()
    {
        $this->login()
            ->assertJsonValidationErrors('email');
    }

    public function test_email_is_required()
    {
        $this->login(['email' => null])
            ->assertJsonValidationErrors(['email' => 'required']);
    }

    public function test_email_must_be_valid()
    {
        $this->login(['email' => 'invalid-email'])
            ->assertJsonValidationErrors(['email' => 'valid email']);
    }

    public function test_device_name_is_required()
    {
        $this->login(['device_name' => null])
            ->assertJsonValidationErrors(['device_name' => 'required']);
    }

    public function test_device_name_must_be_string()
    {
        $this->login(['device_name' => 123])
            ->assertJsonValidationErrors(['device_name' => 'string']);
    }
}
