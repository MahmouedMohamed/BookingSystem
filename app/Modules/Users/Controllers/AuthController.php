<?php

namespace App\Modules\Users\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Users\Exceptions\LoginFailedException;
use App\Modules\Users\Interfaces\AuthServiceInterface;
use App\Modules\Users\Requests\LoginRequest;
use App\Modules\Users\Requests\RegisterRequest;
use App\Modules\Users\Resources\UserResource;
use App\Traits\ApiResponse;
use Exception;

class AuthController
{
    use ApiResponse;

    public function __construct(private AuthServiceInterface $authService) {}

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     description="Creates a new user account and returns the user data along with an authentication token.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="Mahmoued"),
     *             @OA\Property(property="email", type="string", format="email", example="mahmoued31@yahoo.com"),
     *             @OA\Property(property="password", type="string", example="Mahmoued"),
     *             @OA\Property(property="role", type="string", example="customer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="registered_successfully"),
     *             @OA\Property(
     *                 property="items",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=27),
     *                     @OA\Property(property="name", type="string", example="Mahmoued"),
     *                     @OA\Property(property="email", type="string", example="mahmoued31@yahoo.com"),
     *                     @OA\Property(property="timezone", type="string", nullable=true, example=null),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05 01:26:34"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05 01:26:34")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="5|F51GRitikboE7ZU0CEMkJL8Zsasj0ayEYlZWevlV6af18678")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or bad request",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to register: Email already taken")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->register($request);

            return $this->sendSuccessResponse('registered_successfully', ['user' => new UserResource($data['user']), 'token' => $data['token']], 'items', 201);
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to register: '.$e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="User login",
     *     description="Authenticates a user with email and password, returning user details and an access token.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successfully"),
     *             @OA\Property(
     *                 property="item",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin User"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="timezone", type="integer", nullable=true, example=4),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05 00:47:28"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05 00:47:28")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="4|bUqJo1JjqSjW96SO5gNF2vSqVjC2ipwqM52VlBoE8161e38f")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not registered",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="user_not_registered_in_our_system")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid email or password")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to login: unexpected error")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request);

            return $this->sendSuccessResponse('Login successfully', ['user' => new UserResource($data['user']), 'token' => $data['token']], 'item');
        } catch (ModelNotFoundException $e) { // Sometimes this is confidential info (believe it's not here)
            return $this->sendErrorResponse('user_not_registered_in_our_system', 404);
        } catch (LoginFailedException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to login: '.$e->getMessage());
        }
    }
}
