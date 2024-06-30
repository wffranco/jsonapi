<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Auth\Concerns\PermissionsHandler;
use App\JsonApi\Sanctum\Http\Controllers\Auth\LoginController as BaseController;
use App\Models\User;

class LoginController extends BaseController
{
    use PermissionsHandler;

    protected function user($request): ?User
    {
        return User::where('email', $request->email)->first();
    }
}
