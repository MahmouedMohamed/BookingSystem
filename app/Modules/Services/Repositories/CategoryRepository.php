<?php

namespace App\Modules\Services\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Services\Interfaces\CategoryRepositoryInterface;
use App\Modules\Services\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function index($request): LengthAwarePaginator
    {
        $query = Category::when($request->has('search'), function ($query) use ($request) {
            $query->search($request->get('search'));
        });

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function store($request): Category
    {
        $data = $request->validated();

        return Category::create($data);
    }

    public function find($id, $withTrashed = false): Category
    {
        $model = Category::where('id', $id)->withTrashed($withTrashed)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    public function update($request, $category): Category
    {
        $data = $request->validated();

        $category->update($data);

        return $category->fresh();
    }

    public function destroy($category): bool
    {
        return $category->delete();
    }

    public function restore($category): Category
    {
        $category->restore();

        return $category->fresh();
    }
}
