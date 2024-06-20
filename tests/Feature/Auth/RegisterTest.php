<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use Auth, RefreshDatabase;

    public function test_name_is_required()
    {
        $this->register(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    public function test_name_must_be_a_string()
    {
        $this->register(['name' => 123])
            ->assertJsonValidationErrors('name');
    }

    public function test_email_is_required()
    {
        $this->register(['email' => null])
            ->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_a_valid_email()
    {
        $this->register(['email' => 'invalid-email'])
            ->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_unique()
    {
        $user = User::factory()->create();
        $this->register(['email' => $user->email])
            ->assertJsonValidationErrors('email');
    }

    public function test_alias_is_required()
    {
        $this->register(['alias' => null])
            ->assertJsonValidationErrors('alias');
    }

    public function test_alias_must_be_a_string()
    {
        $this->register(['alias' => 123])
            ->assertJsonValidationErrors('alias');
    }

    public function test_alias_must_be_unique()
    {
        $user = User::factory()->create();
        $this->register(['alias' => $user->alias])
            ->assertJsonValidationErrors('alias');
    }

    public function test_password_is_required()
    {
        $this->register(['password' => null])
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_be_confirmed()
    {
        $this->register(['password' => 'password', 'password_confirmation' => 'wrong-password'])
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $this->register(['password' => '1234567', 'password_confirmation' => '1234567'])
            ->assertJsonValidationErrors('password');
    }

    public function test_device_name_must_be_a_string()
    {
        $this->register(['device_name' => 123])
            ->assertJsonValidationErrors('device_name');
    }

    public function test_can_register()
    {
        $response = $this->register();
        $token = $response->json('token');
        $this->assertDatabaseCount('users', 1);

        $user = User::first();
        $dbToken = PersonalAccessToken::findToken($token);
        $this->assertTrue($dbToken->tokenable->is($user));

        $this->assertDatabaseHas('users', [
            'alias' => $user->alias,
            'email' => $user->email,
        ]);
    }

    public function test_cannot_register_again_with_the_same_email()
    {
        $user = User::factory()->create();
        $this->register(['email' => $user->email])
            ->assertStatus(422);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_cannot_register_again_with_the_same_alias()
    {
        $user = User::factory()->create();
        $this->register(['alias' => $user->alias])
            ->assertStatus(422);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_cannot_register_if_already_authenticated()
    {
        $user = User::factory()->create();
        $this->logged($user)->register()
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthorized.']);

        $this->assertDatabaseCount('users', 1);
    }
}
