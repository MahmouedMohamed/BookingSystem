<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;

class ModelNotFoundException extends Exception
{
    use ApiResponse;

    public function __construct() {}

    public function render()
    {
        return $this->error(__('model_not_found'), 404);
    }
}
