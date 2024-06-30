<?php

namespace App\JsonApi\Sanctum\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\JsonApi\Sanctum\Http\Resource\Json\TokenResource;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

abstract class LoginController extends Controller implements HasMiddleware
{
    const DEVICE_FIELD = 'device_name';

    public static function middleware(): array
    {
        return [
            new Middleware('guest:sanctum'),
        ];
    }

    protected function getDeviceName(Request $request): ?string
    {
        return $request->input(static::DEVICE_FIELD);
    }

    /** @param User $user */
    abstract protected function getPermissions($user): array;

    /** @param HasApiTokens $user */
    protected function getToken($user, Request $request): NewAccessToken
    {
        return $user->createToken(
            $this->getDeviceName($request),
            $this->getPermissions($user),
        );
    }

    protected function rules(array $rules = []): array
    {
        return $rules + [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            static::DEVICE_FIELD => ['required', 'string'],
        ];
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  Request  $request
     * @return null|HasApiTokens
     */
    abstract protected function user($request);

    /** @return TokenResource */
    public function __invoke(Request $request): JsonResource|JsonResponse
    {
        $request->validate($this->rules());

        $user = $this->user($request);

        if (! $user || ! \Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return TokenResource::make($this->getToken($user, $request));
    }
}
