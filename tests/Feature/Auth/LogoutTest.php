<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_logout()
    {
        $response = $this->postJson(route('api.v1.login'), [
            'email' => User::factory()->create()->email,
            'password' => 'password',
            'device_name' => 'test',
        ], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertOk();
        $token = $response->json('plain-text-token');
        $this->assertIsString($token);

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('api.v1.logout'), [], [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ])
            ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($token));
    }
}
