<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use Auth, RefreshDatabase;

    public function test_can_logout()
    {
        $user = User::factory()->create();
        $token = $this->token($user);
        $this->logged(token: $token)->logout()
            ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($token));
    }

    public function test_cannot_logout_if_not_authenticated()
    {
        $this->logout()
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
