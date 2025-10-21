<?php

namespace App\Repositories;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServiceRepository implements AuthInterface
{

    /**
     * @param array $data
     * @return User|null
     */
    public function authenticate(array $data): ?User
    {
      $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)){
           return null;
        } else return $user;

    }


}
