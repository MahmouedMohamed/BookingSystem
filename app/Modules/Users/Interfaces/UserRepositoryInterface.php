<?php

namespace App\Modules\Users\Interfaces;

use App\Modules\Users\Models\User;

interface UserRepositoryInterface
{
    public function store($request): User;

    public function findByEmail($request): User;
}
