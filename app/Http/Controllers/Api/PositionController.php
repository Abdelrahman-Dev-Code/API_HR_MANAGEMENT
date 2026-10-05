<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Http\Resources\PositionResource;
use App\Repositories\Position\PositionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PositionController extends Controller
{
    public function __construct(private readonly PositionRepositoryInterface $positions) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return PositionResource::collection($this->positions->paginate($request->integer('per_page', 15)));
    }

    public function store(StorePositionRequest $request): JsonResponse
    {
        return (new PositionResource($this->positions->create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $position): PositionResource
    {
        return new PositionResource($this->positions->find($position));
    }

    public function update(UpdatePositionRequest $request, int $position): PositionResource
    {
        return new PositionResource($this->positions->update($this->positions->find($position), $request->validated()));
    }

    public function destroy(int $position): Response
    {
        $this->positions->delete($this->positions->find($position));

        return response()->noContent();
    }
}
