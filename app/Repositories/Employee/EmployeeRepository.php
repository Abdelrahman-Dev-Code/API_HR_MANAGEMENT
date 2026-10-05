<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new Employee());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with(['department', 'position']);
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'employees') : $query;
    }
}
