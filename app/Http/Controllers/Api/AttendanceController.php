<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Repositories\Attendance\AttendanceRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendances,
        private readonly DataVisibility $visibility,
    )
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return AttendanceResource::collection($this->attendances->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->visibility->assertEmployeeInScope($request->user(), (int) $data['employee_id'], 'attendance');

        return (new AttendanceResource($this->attendances->create($data)))->response()->setStatusCode(201);
    }

    public function show(int $attendance): AttendanceResource
    {
        return new AttendanceResource($this->attendances->find($attendance));
    }

    public function update(UpdateAttendanceRequest $request, int $attendance): AttendanceResource
    {
        $model = $this->attendances->find($attendance);
        $data = $request->validated();
        $employeeId = (int) ($data['employee_id'] ?? $model->employee_id);
        $this->visibility->assertEmployeeInScope($request->user(), $employeeId, 'attendance');

        return new AttendanceResource($this->attendances->update($model, $data));
    }
}
