<?php

namespace App\Modules\Services\Interfaces;

use App\Modules\Services\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function index($request): LengthAwarePaginator;

    public function store($request): Category;

    public function find($id, $withTrashed = false): Category;

    public function update($request, $category): Category;

    public function destroy($category): bool;

    public function restore($id): Category;
}
