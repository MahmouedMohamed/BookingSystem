<?php

namespace App\Modules\Users\Interfaces;

interface AuthServiceInterface
{
    public function register($request);

    public function login($request);
}
