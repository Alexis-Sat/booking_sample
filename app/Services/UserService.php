<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class UserService
{
    private UserRepositoryInterface $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Show meeting user
     * @param User $user
     * @return User
     */
    public function show(User $user): User
    {
        return $this->userRepository->getById($user->id);
    }

    /**
     * Store user
     * @param $data
     * @return User
     */
    public function store($data): User
    {
        return $this->userRepository->store($data);
    }

    /**
     * Update user
     * @param $data
     * @param User $user
     * @return User
     */
    public function update($data, User $user): User
    {
        return $this->userRepository->update($data, $user->id);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function destroy(User $user):bool
    {
       return $this->userRepository->delete($user->id);
    }

    /**
     * @param User $user
     * @return User
     */
    public function me(User $user):User
    {
        return  $this->userRepository->getById($user->id);
    }
}
