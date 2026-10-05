<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employees,
        private readonly DataVisibility $visibility,
    )
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return EmployeeResource::collection($this->employees->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->visibility->assertDepartmentInScope($request->user(), (int) $data['department_id'], 'employees');
        $employee = $this->employees->create($data);

        return (new EmployeeResource($employee))->response()->setStatusCode(201);
    }

    public function show(int $employee): EmployeeResource
    {
        return new EmployeeResource($this->employees->find($employee));
    }

    public function update(UpdateEmployeeRequest $request, int $employee): EmployeeResource
    {
        $model = $this->employees->find($employee);
        $data = $request->validated();

        if (isset($data['department_id'])) {
            $this->visibility->assertDepartmentInScope($request->user(), (int) $data['department_id'], 'employees');
        }

        return new EmployeeResource($this->employees->update($model, $data));
    }

    public function destroy(int $employee): Response
    {
        $this->employees->delete($this->employees->find($employee));

        return response()->noContent();
    }
}
