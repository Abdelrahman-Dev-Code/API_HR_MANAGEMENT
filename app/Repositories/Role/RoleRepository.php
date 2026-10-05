<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\Shared\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Role());
    }

    protected function query(): Builder
    {
        return parent::query()->with('permissions');
    }

    public function syncPermissions(Role $role, array $permissionIds): Role
    {
        $role->permissions()->sync($permissionIds);

        return $role->refresh()->load('permissions');
    }
}
