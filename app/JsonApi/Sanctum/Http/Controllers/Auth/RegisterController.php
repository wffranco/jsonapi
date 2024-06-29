<?php

namespace App\JsonApi\Sanctum\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RegisterController extends LoginController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('guest:sanctum'),
        ];
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'device_name' => 'string',
        ]);
        User::create($request->only('alias', 'name', 'email', 'password'));

        if (! $request->has('device_name')) {
            return response()->json(['message' => 'User registered successfully'], 201);
        }

        return parent::__invoke($request);
    }
}
