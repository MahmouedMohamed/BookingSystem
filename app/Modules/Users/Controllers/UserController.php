<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Interfaces\UserServiceInterface;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\StoreUserRequest;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Resources\UserCollectionResource;
use App\Modules\Users\Resources\UserResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private UserServiceInterface $userService) {}

    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);

            $users = $this->userService->index($request);

            return $this->sendSuccessResponse('Users retrieved successfully', new UserCollectionResource($users));
        } catch(AuthorizationException $e){
            throw $e;
        }catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve users: '.$e->getMessage());
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->authorize('create', User::class);

            $user = $this->userService->store($request);

            return $this->sendSuccessResponse('User created successfully', new UserResource($user), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create user: '.$e->getMessage());
        }
    }

    public function show(User $user)
    {
        try {
            $this->authorize('view', $user);

            return $this->sendSuccessResponse('User retrieved successfully', new UserResource($user), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('User not found', 404);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->authorize('update', $user);

            $user = $this->userService->update($request, $user);

            return $this->sendSuccessResponse('User updated successfully', new UserResource($user), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update user: '.$e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->authorize('delete', $user);

            $this->userService->destroy($user);

            return $this->sendSuccessResponse('User deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete user: '.$e->getMessage());
        }
    }

    public function restore(string $id)
    {
        try {
            $this->authorize('restore', User::class);

            $user = $this->userService->restore($id);

            return $this->sendSuccessResponse('User restored successfully', new UserResource($user), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to restore user: '.$e->getMessage());
        }
    }
}
