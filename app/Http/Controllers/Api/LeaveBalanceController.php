<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveBalance\StoreLeaveBalanceRequest;
use App\Http\Requests\LeaveBalance\UpdateLeaveBalanceRequest;
use App\Http\Resources\LeaveBalanceResource;
use App\Repositories\LeaveBalance\LeaveBalanceRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LeaveBalanceController extends Controller
{
    public function __construct(
        private readonly LeaveBalanceRepositoryInterface $balances,
        private readonly DataVisibility $visibility,
    )
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return LeaveBalanceResource::collection($this->balances->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreLeaveBalanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->visibility->assertEmployeeInScope($request->user(), (int) $data['employee_id'], 'leave-balances');

        return (new LeaveBalanceResource($this->balances->create($data)))->response()->setStatusCode(201);
    }

    public function show(int $leaveBalance): LeaveBalanceResource
    {
        return new LeaveBalanceResource($this->balances->find($leaveBalance));
    }

    public function update(UpdateLeaveBalanceRequest $request, int $leaveBalance): LeaveBalanceResource
    {
        $model = $this->balances->find($leaveBalance);
        $data = $request->validated();

        if (isset($data['employee_id'])) {
            $this->visibility->assertEmployeeInScope($request->user(), (int) $data['employee_id'], 'leave-balances');
        }

        return new LeaveBalanceResource($this->balances->update($model, $data));
    }

    public function destroy(int $leaveBalance): Response
    {
        $this->balances->delete($this->balances->find($leaveBalance));

        return response()->noContent();
    }
}
