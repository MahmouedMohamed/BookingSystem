<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Services\Interfaces\CategoryServiceInterface;
use App\Modules\Services\Models\Category;
use App\Modules\Services\Requests\CategoryRequest;
use App\Modules\Services\Resources\CategoryCollectionResource;
use App\Modules\Services\Resources\CategoryResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(private CategoryServiceInterface $categoryServiceInterface) {}

    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Category::class);

            $categories = $this->categoryServiceInterface->index($request);

            return $this->sendSuccessResponse('Categories retrieved successfully', new CategoryCollectionResource($categories));
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve categories: '.$e->getMessage());
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            $this->authorize('create', Category::class);

            $category = $this->categoryServiceInterface->store($request);

            return $this->sendSuccessResponse('Category created successfully', new CategoryResource($category), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create category: '.$e->getMessage());
        }
    }

    public function show(Category $category)
    {
        try {
            $this->authorize('view', $category);

            return $this->sendSuccessResponse('Category retrieved successfully', new CategoryResource($category), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Category not found', 404);
        }
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $this->authorize('update', $category);

            $category = $this->categoryServiceInterface->update($request, $category);

            return $this->sendSuccessResponse('Category updated successfully', new CategoryResource($category), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update category: '.$e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->authorize('delete', $category);

            $this->categoryServiceInterface->destroy($category);

            return $this->sendSuccessResponse('Category deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete category: '.$e->getMessage());
        }
    }

    public function restore(string $id)
    {
        try {
            $this->authorize('restore', Category::class);

            $this->categoryServiceInterface->restore($id);

            return $this->sendSuccessResponse('Category restored successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to restored category: '.$e->getMessage());
        }
    }
}
