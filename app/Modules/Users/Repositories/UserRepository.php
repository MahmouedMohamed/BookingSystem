<?php

namespace App\Modules\Users\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Users\Interfaces\UserRepositoryInterface;
use App\Modules\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function index($request): LengthAwarePaginator
    {
        $query = User::when($request->has('search'), function ($query) use ($request) {
            $query->search($request->get('search'));
        })
            ->when($request->has('role'), function ($query) use ($request) {
                $query->role($request->get('role'));
            });

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function store($request): User
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function find($id, $withTrashed = false): User
    {
        $model = User::where('id', $id)->withTrashed($withTrashed)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    public function findByEmail($email): User
    {
        $model = User::where('email', $email)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    public function update($request, $user): User
    {
        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function destroy($user): bool
    {
        return $user->delete();
    }

    public function restore($user): User
    {
        $user->restore();

        return $user->fresh();
    }
}
