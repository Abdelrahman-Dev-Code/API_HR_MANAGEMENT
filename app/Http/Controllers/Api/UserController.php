<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(User::query()
            ->with(['roles:id,role_name,display_name', 'employee:id,user_id,employee_code'])
            ->latest('id')
            ->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create(collect($data)->except('role_ids')->all());
            $user->roles()->sync($data['role_ids'] ?? []);

            return $user->load(['roles:id,role_name,display_name', 'employee:id,user_id,employee_code']);
        });

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();
        DB::transaction(function () use ($data, $user): void {
            $user->fill(collect($data)->except(['role_ids', 'password'])->all());

            if (filled($data['password'] ?? null)) {
                $user->password = $data['password'];
            }

            $user->save();

            if (array_key_exists('role_ids', $data)) {
                $user->roles()->sync($data['role_ids']);
            }
        });

        return new UserResource($user->load(['roles:id,role_name,display_name', 'employee:id,user_id,employee_code']));
    }
}
