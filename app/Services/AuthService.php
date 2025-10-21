<?php

namespace App\Services;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthService
{

    /**
     * @var AuthInterface
     */
    private AuthInterface $auth;

    /**
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $data
     * @return array
     */
    public function login($data):array
    {
        $user = $this->auth->authenticate($data);
        if (!$user) {
            throw new UnauthorizedHttpException('Wrong username or password');
        }

        $token = $this->createToken($user);

        return [
            'auth_token' => $token,
            'user' => $user,
        ];
    }

    /**
     * @param User $user
     * @return string
     */
    private function createToken(User $user): string
    {
       return $user->createToken('auth-token')->plainTextToken;

    }

    /**
     * @param User $user
     * @return bool
     */
    public function logout(User $user):bool
    {
       return $user->currentAccessToken()->delete();
    }

}
