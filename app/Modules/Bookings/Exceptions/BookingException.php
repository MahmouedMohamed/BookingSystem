<?php

namespace App\Modules\Bookings\Exceptions;

use App\Traits\ApiResponse;
use Exception;

class BookingException extends Exception
{
    use ApiResponse;

    public function __construct(protected string $customMessage, protected int $statusCode) {}

    public function render()
    {
        return $this->sendErrorResponse($this->customMessage, $this->statusCode);
    }
}
