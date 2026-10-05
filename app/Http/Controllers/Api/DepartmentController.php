<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Repositories\Department\DepartmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentRepositoryInterface $departments) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return DepartmentResource::collection($this->departments->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        return (new DepartmentResource($this->departments->create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $department): DepartmentResource
    {
        return new DepartmentResource($this->departments->find($department));
    }

    public function update(UpdateDepartmentRequest $request, int $department): DepartmentResource
    {
        return new DepartmentResource($this->departments->update($this->departments->find($department), $request->validated()));
    }

    public function destroy(int $department): Response
    {
        $this->departments->delete($this->departments->find($department));

        return response()->noContent();
    }
}
