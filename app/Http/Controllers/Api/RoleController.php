<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct(private readonly RoleRepositoryInterface $roles)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return RoleResource::collection($this->roles->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);
        $role = $this->roles->create($data);

        return (new RoleResource($this->roles->syncPermissions($role, $permissionIds)))->response()->setStatusCode(201);
    }

    public function show(int $role): RoleResource
    {
        return new RoleResource($this->roles->find($role));
    }

    public function update(UpdateRoleRequest $request, int $role): RoleResource
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);
        $model = $this->roles->update($this->roles->find($role), $data);

        return new RoleResource($permissionIds === null ? $model : $this->roles->syncPermissions($model, $permissionIds));
    }

    public function destroy(int $role): Response
    {
        $this->roles->delete($this->roles->find($role));

        return response()->noContent();
    }
}
