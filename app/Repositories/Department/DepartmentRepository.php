<?php

namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\Shared\BaseRepository;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Department());
    }
}
