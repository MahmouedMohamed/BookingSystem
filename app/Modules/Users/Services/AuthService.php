<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Exceptions\LoginFailedException;
use App\Modules\Users\Interfaces\AuthServiceInterface;
use App\Modules\Users\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function register($request)
    {
        $user = $this->userRepository->store($request);
        $token = $user->createToken('')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login($request)
    {
        $user = $this->userRepository->findByEmail($request['email']);

        if (! Hash::check($request->password, $user->password)) {
            throw new LoginFailedException;
        }

        $token = $user->createToken('')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
