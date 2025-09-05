<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\PathItem(path="/api")
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="My Laravel API",
 *     description="This is the API documentation for my Laravel project."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */
abstract class Controller
{
    use AuthorizesRequests;
}
