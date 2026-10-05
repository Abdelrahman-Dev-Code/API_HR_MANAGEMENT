<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'employee_code' => $this->employee_code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'basic_salary' => $this->basic_salary,
            'hire_date' => $this->hire_date?->toDateString(),
            'birth_date' => $this->birth_date?->toDateString(),
            'address' => $this->address,
            'gender' => $this->gender,
            'profile_photo' => $this->profile_photo,
            'status' => $this->status,
            'work_shift' => $this->work_shift,
            'department' => $this->whenLoaded('department', fn (): array => [
                'id' => $this->department->id,
                'name' => $this->department->department_name,
                'code' => $this->department->code,
            ]),
            'position' => $this->whenLoaded('position', fn (): array => [
                'id' => $this->position->id,
                'name' => $this->position->position_name,
                'code' => $this->position->code,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
