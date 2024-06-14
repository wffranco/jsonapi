<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\JsonApi\JsonApiResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $user = User::paginated([]);

        return AuthorResource::collection($user);
    }

    /**
     * Display the specified resource.
     */
    public function show($user): JsonApiResource
    {
        $user = User::findOrFail($user);

        return AuthorResource::make($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): JsonApiResource
    {
        $user = User::create($request->validated('attributes'));

        return AuthorResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAuthorRequest $request, User $user): JsonApiResource
    {
        $user->update($request->validated('attributes'));

        return AuthorResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
