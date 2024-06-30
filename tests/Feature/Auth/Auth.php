<?php

namespace Tests\Feature\Auth;

use App\Models\User;

/** @mixin \Illuminate\Foundation\Testing\TestCase */
trait Auth
{
    protected function logged(?User $user = null, ?string $token = null)
    {
        $token ??= $this->token($user);

        return $this->withHeader('Authorization', "Bearer $token");
    }

    protected function login(array $data = [], ?User $user = null)
    {
        $user ??= User::factory()->make(); // Unregistered user

        // Add credentials and headers to the request
        return $this->postJson(route('api.v1.auth.login'), array_merge([
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test',
        ], $data));
    }

    protected function logout()
    {
        return $this->postJson(route('api.v1.auth.logout'), []);
    }

    protected function register(array $data = [], ?User $user = null)
    {
        $user ??= User::factory()->make(); // provide an unregistered user

        return $this->postJson(route('api.v1.auth.register'), array_filter(array_merge([
            'alias' => $user->alias,
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'test',
        ], $data)));
    }

    /**
     * Through login, we can get the token for the user.
     */
    protected function token(?User $user = null)
    {
        $response = $this->login(user: $user);
        $token = $response->json('token');
        $this->assertIsString($token);

        return $token;
    }
}
