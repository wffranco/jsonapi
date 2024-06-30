<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Auth\Concerns\PermissionsHandler;
use App\JsonApi\Sanctum\Http\Controllers\Auth\RegisterController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{
    use PermissionsHandler;

    public function rules(array $rules = []): array
    {
        return parent::rules($rules + [
            'alias' => ['required', 'string', 'max:255', 'unique:users'],
        ]);
    }

    /**
     * @param  Request  $request
     * @return null|User
     */
    public function user($request)
    {
        return User::create($request->only('alias', 'name', 'email', 'password'));
    }
}
