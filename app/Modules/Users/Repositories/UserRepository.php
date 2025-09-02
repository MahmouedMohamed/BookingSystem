<?php

namespace App\Modules\Users\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Users\Interfaces\UserRepositoryInterface;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function store($request): User
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function findByEmail($email): User
    {
        $model = User::where('email', $email)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }
}
