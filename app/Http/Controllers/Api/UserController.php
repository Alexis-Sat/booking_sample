<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 *
 */
class UserController extends Controller
{
    private userService $userService;

    /**
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /** Returns meeting users
     * @param IndexRequest $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $users = $this->userService->index();
        return UserResource::collection($users);
    }

    /**
     * Show meeting user
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        $user = $this->userService->show($user);
        return new UserResource($user);
    }

    /**
     * Store user
     * @param StoreRequest $request
     * @return UserResource|JsonResponse
     */
    public function store(StoreRequest $request): UserResource|JsonResponse
    {
        try {

            $userData = $request->validated();
            $user = $this->userService->store($userData);
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

    }

    /**
     * Update user
     * @param UpdateRequest $request
     * @param User $user
     * @return UserResource|JsonResponse
     */
    public function update(UpdateRequest $request, User $user): UserResource|JsonResponse
    {
        try {
            $userData = $request->validated();
            $user = $this->userService->update($userData, $user->id);
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user):JsonResponse
    {
        $result = $this->userService->destroy($user);
        if ($result) {
            return response()->json('Deleted', 204);
        } else {
            return response()->json('Not found', 404);
        }
    }

    /**
     * @return UserResource
     */
    public function me():UserResource
    {
        $user = $this->userService->show(auth()->user());
        return new UserResource($user);
    }

}
