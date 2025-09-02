<?php

namespace App\Modules\Users\Interfaces;

use App\Modules\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function index($request): LengthAwarePaginator;

    public function store($request): User;

    public function update($request, $user): User;

    public function destroy($user): bool;

    public function restore($id): User;
}
