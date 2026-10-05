<?php

namespace App\Repositories\Salary;

use App\Models\Salary;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalaryRepository extends BaseRepository implements SalaryRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new Salary());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with('employee');
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'payroll') : $query;
    }
}
