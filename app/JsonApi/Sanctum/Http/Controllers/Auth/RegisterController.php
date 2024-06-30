<?php

namespace App\JsonApi\Sanctum\Http\Controllers\Auth;

use App\JsonApi\Sanctum\Http\Resource\Json\TokenResource;
use App\Models\User;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

abstract class RegisterController extends LoginController
{
    protected function rules(array $rules = []): array
    {
        return $rules + [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            static::DEVICE_FIELD => ['string'],
        ];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  Request  $request
     * @return null|User
     *
     * @throws MassAssignmentException
     */
    abstract protected function user($request);

    public function __invoke(Request $request): JsonResource|JsonResponse
    {
        $request->validate($this->rules());

        $user = $this->user($request);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['User registration failed.'],
            ]);
        }

        if ($this->getDeviceName($request)) {
            return TokenResource::make($this->getToken($user, $request));
        }

        return response()->json(['message' => 'User registered successfully'], 201);
    }
}
