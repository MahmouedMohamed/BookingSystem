<?php

namespace App\Modules\Users\Interfaces;

interface AuthRepositoryInterface
{
    public function register($request);

    public function login($request);
}
