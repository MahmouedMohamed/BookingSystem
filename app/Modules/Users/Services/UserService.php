<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Interfaces\UserRepositoryInterface;
use App\Modules\Users\Interfaces\UserServiceInterface;
use App\Modules\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function index($request): LengthAwarePaginator
    {
        return $this->userRepository->index($request);
    }

    public function store($request): User
    {
        return $this->userRepository->store($request);
    }

    public function update($request, $user): User
    {
        return $this->userRepository->update($request, $user);
    }

    public function destroy($user): bool
    {
        return $this->userRepository->destroy($user);
    }

    public function restore($id): User
    {
        $user = $this->userRepository->find($id, true);

        return $this->userRepository->restore($user);
    }
}
