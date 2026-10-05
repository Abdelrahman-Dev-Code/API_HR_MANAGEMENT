<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Repositories\Setting\SettingRepositoryInterface;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function __construct(private readonly SettingRepositoryInterface $settings)
    {
    }

    public function show(): JsonResponse
    {
        return (new OrganizationResource($this->settings->company()))
            ->response()
            ->setStatusCode(200);
    }

    public function update(UpdateOrganizationRequest $request): OrganizationResource
    {
        return new OrganizationResource($this->settings->updateCompany($request->validated()));
    }
}
