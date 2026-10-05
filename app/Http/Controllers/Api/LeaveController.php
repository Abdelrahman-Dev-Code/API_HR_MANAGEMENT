<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\DecideLeaveRequest;
use App\Http\Requests\Leave\StoreLeaveRequest;
use App\Http\Requests\Leave\UpdateLeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use App\Services\Leave\LeaveWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LeaveController extends Controller
{
    public function __construct(
        private readonly LeaveRepositoryInterface $leaves,
        private readonly LeaveWorkflowService $workflow,
        private readonly DataVisibility $visibility,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return LeaveResource::collection($this->leaves->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreLeaveRequest $request): JsonResponse
    {
        $this->visibility->assertEmployeeInScope($request->user(), (int) $request->validated('employee_id'), 'leaves');

        return (new LeaveResource($this->workflow->submit($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $leave): LeaveResource
    {
        return new LeaveResource($this->leaves->find($leave));
    }

    public function update(UpdateLeaveRequest $request, int $leave): LeaveResource
    {
        return new LeaveResource($this->workflow->updatePending($this->leaves->find($leave), $request->validated()));
    }

    public function decide(DecideLeaveRequest $request, int $leave): LeaveResource
    {
        return new LeaveResource($this->workflow->decide(
            $this->leaves->find($leave),
            $request->user(),
            $request->validated('decision'),
            $request->validated('rejection_reason'),
        ));
    }

    public function destroy(int $leave): Response
    {
        $this->workflow->deletePending($this->leaves->find($leave));

        return response()->noContent();
    }
}
