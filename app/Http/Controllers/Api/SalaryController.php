<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salary\DecideSalaryRequest;
use App\Http\Requests\Salary\StoreSalaryRequest;
use App\Http\Requests\Salary\UpdateSalaryRequest;
use App\Http\Resources\SalaryResource;
use App\Repositories\Salary\SalaryRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use App\Services\Salary\SalaryWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SalaryController extends Controller
{
    public function __construct(
        private readonly SalaryRepositoryInterface $salaries,
        private readonly SalaryWorkflowService $workflow,
        private readonly DataVisibility $visibility,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return SalaryResource::collection($this->salaries->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreSalaryRequest $request): JsonResponse
    {
        $this->visibility->assertEmployeeInScope($request->user(), (int) $request->validated('employee_id'), 'payroll');

        return (new SalaryResource($this->workflow->createDraft($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $salary): SalaryResource
    {
        return new SalaryResource($this->salaries->find($salary));
    }

    public function update(UpdateSalaryRequest $request, int $salary): SalaryResource
    {
        $model = $this->salaries->find($salary);
        $data = $request->validated();

        if (isset($data['employee_id'])) {
            $this->visibility->assertEmployeeInScope($request->user(), (int) $data['employee_id'], 'payroll');
        }

        return new SalaryResource($this->workflow->updateDraft(
            $model,
            $data,
        ));
    }

    public function decide(DecideSalaryRequest $request, int $salary): SalaryResource
    {
        return new SalaryResource($this->workflow->decide(
            $this->salaries->find($salary),
            $request->user(),
            $request->validated('decision'),
        ));
    }

    public function pay(Request $request, int $salary): SalaryResource
    {
        return new SalaryResource($this->workflow->markPaid($this->salaries->find($salary), $request->user()));
    }

    public function destroy(int $salary): Response
    {
        $this->workflow->deleteDraft($this->salaries->find($salary));

        return response()->noContent();
    }
}
