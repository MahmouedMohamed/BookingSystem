<?php

namespace App\Modules\Services\Services;

use App\Modules\Services\Interfaces\CategoryRepositoryInterface;
use App\Modules\Services\Interfaces\CategoryServiceInterface;
use App\Modules\Services\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function index($request): LengthAwarePaginator
    {
        return $this->categoryRepository->index($request);
    }

    public function store($request): Category
    {
        return $this->categoryRepository->store($request);
    }

    public function update($request, $category): Category
    {
        return $this->categoryRepository->update($request, $category);
    }

    public function destroy($category): bool
    {
        return $this->categoryRepository->destroy($category);
    }

    public function restore($id): Category
    {
        $category = $this->categoryRepository->find($id, true);

        return $this->categoryRepository->restore($category);
    }
}
