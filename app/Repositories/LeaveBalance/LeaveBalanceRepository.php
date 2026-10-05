<?php

namespace App\Repositories\LeaveBalance;

use App\Models\LeaveBalance;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaveBalanceRepository extends BaseRepository implements LeaveBalanceRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new LeaveBalance());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with('employee');
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'leave-balances') : $query;
    }
}
