<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\Contracts\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
	public function syncPermissions(Role $role, array $permissionIds): Role;
}
