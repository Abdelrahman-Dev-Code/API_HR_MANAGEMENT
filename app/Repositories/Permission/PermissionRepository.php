<?php

namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\Shared\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Permission());
    }

    protected function query(): Builder
    {
        return parent::query()->with('roles');
    }
}
