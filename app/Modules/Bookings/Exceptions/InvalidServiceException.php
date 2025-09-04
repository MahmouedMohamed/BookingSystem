<?php

namespace App\Modules\Bookings\Exceptions;

use App\Traits\ApiResponse;
use Exception;

class InvalidServiceException extends Exception
{
    use ApiResponse;

    public function __construct() {}

    public function render()
    {
        return $this->sendErrorResponse(__('invalid_service'), 403);
    }
}
