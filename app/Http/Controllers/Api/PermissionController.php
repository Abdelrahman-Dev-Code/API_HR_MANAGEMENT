<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Repositories\Permission\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionRepositoryInterface $permissions)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return PermissionResource::collection($this->permissions->paginate($request->integer('per_page', 15)));
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        return (new PermissionResource($this->permissions->create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $permission): PermissionResource
    {
        return new PermissionResource($this->permissions->find($permission));
    }

    public function update(UpdatePermissionRequest $request, int $permission): PermissionResource
    {
        return new PermissionResource($this->permissions->update($this->permissions->find($permission), $request->validated()));
    }

    public function destroy(int $permission): Response
    {
        $this->permissions->delete($this->permissions->find($permission));

        return response()->noContent();
    }
}
