<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 *
 */
class UserController extends Controller
{
    /** Returns meeting users
     * @param IndexRequest $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Show meeting user
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Store user
     * @param StoreRequest $request
     * @return UserResource
     */
    public function store(StoreRequest $request): UserResource
    {
        $userData = $request->validated();
        $user = User::create($userData);
        return new UserResource($user);

    }

    /**
     * Update user
     * @param User $user
     * @param UpdateRequest $request
     * @return UserResource
     */
    public function update(UpdateRequest $request, User $user): UserResource
    {
        $userData = $request->validated();
        $user->update($userData);
        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user):JsonResponse
    {
        $user->delete();
        return response()->json('Deleted', 204);
    }

    public function me():JsonResponse
    {
        return response()->json(auth()->user());
    }

}
