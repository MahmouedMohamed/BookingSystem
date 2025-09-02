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

    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->register($request);

            return $this->success('registered_successfully', ['user' => new UserResource($data['user']), 'token' => $data['token']]);
        } catch (Exception $e) {
            return $this->error('Failed to register: '.$e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request);

            return $this->success('Login successfully', ['user' => new UserResource($data['user']), 'token' => $data['token']], 'item');
        } catch(ModelNotFoundException $e){ // Sometimes this is confidential info (believe it's not here)
            return $this->error('user_not_registered_in_our_system', 404);
        } catch(LoginFailedException $e){
            throw $e;
        } catch (Exception $e) {
            return $this->error('Failed to login: '.$e->getMessage());
        }
    }
}
