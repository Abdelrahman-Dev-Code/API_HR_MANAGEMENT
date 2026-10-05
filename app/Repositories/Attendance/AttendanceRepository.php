<?php

namespace App\Repositories\Attendance;

use App\Models\Attendance;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new Attendance());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with('employee');
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'attendance') : $query;
    }
}
