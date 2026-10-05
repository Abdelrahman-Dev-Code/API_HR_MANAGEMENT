<?php

namespace App\Repositories\Leave;

use App\Models\Leave;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaveRepository extends BaseRepository implements LeaveRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new Leave());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with(['employee', 'approver']);
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'leaves') : $query;
    }
}
