<?php

namespace App\Modules\Users\Exceptions;

use App\Traits\ApiResponse;
use Exception;

class LoginFailedException extends Exception
{
    use ApiResponse;

    public function __construct() {}

    public function render()
    {
        return $this->error(__('invalid_credentials'), 401);
    }
}
