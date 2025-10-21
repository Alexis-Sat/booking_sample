<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
       return User::all();
    }

    /**
     * @param int $id
     * @return User
     */
    public function getById(int $id):User
    {
        return User::query()->find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): User
    {
       return User::create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return User
     */
    public function update(array $data, $id):User
    {
        $user = User::query()->find($id);
        $user->update($data);
        return $user;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): bool
    {
        return User::where('id',$id)->delete();
    }
}
