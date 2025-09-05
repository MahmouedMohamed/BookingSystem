<?php

namespace App\Modules\Services\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Services\Interfaces\ServiceRepositoryInterface;
use App\Modules\Services\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function index($request, $with = []): LengthAwarePaginator
    {
        $query = Service::with($with)
            ->when(Auth::user()->role == 'customer', function ($query) {
                return $query->published();
            })->when(Auth::user()->role == 'provider', function ($query) {
                return $query->provider(Auth::user()->id);
            })->when($request->has('search'), function ($query) use ($request) {
                return $query->search($request->get('search'));
            })->when($request->with_trashed, function ($query) {
                return $query->withTrashed();
            })->when(Auth::user()->role == 'admin' && $request->provider_id, function ($query) use ($request) {
                return $query->provider($request->provider_id);
            });

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function store($request): Service
    {
        $data = $request->validated();

        $data['provider_id'] = Auth::id();

        return Service::create($data);
    }

    public function find($id, $withTrashed = false): Service
    {
        $model = Service::where('id', $id)->withTrashed($withTrashed)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    public function update($request, $service): Service
    {
        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $service->update($data);

        return $service->fresh();
    }

    public function destroy($service): bool
    {
        return $service->delete();
    }

    public function restore($service): Service
    {
        $service->restore();

        return $service->fresh();
    }
}
