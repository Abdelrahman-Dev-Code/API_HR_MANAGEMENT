<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Holiday\StoreHolidayRequest;
use App\Http\Requests\Holiday\UpdateHolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Repositories\Holiday\HolidayRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class HolidayController extends Controller
{
    public function __construct(private readonly HolidayRepositoryInterface $holidays)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return HolidayResource::collection($this->holidays->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreHolidayRequest $request): JsonResponse
    {
        return (new HolidayResource($this->holidays->create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(int $holiday): HolidayResource
    {
        return new HolidayResource($this->holidays->find($holiday));
    }

    public function update(UpdateHolidayRequest $request, int $holiday): HolidayResource
    {
        return new HolidayResource($this->holidays->update($this->holidays->find($holiday), $request->validated()));
    }

    public function destroy(int $holiday): Response
    {
        $this->holidays->delete($this->holidays->find($holiday));

        return response()->noContent();
    }
}
