<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->company_name,
            'logo' => $this->company_logo,
            'timezone' => $this->timezone,
            'currency_code' => $this->currency_code,
            'work_start_time' => $this->work_start_time,
            'work_end_time' => $this->work_end_time,
            'day_shift_start_time' => $this->day_shift_start_time,
            'day_shift_end_time' => $this->day_shift_end_time,
            'night_shift_start_time' => $this->night_shift_start_time,
            'night_shift_end_time' => $this->night_shift_end_time,
            'weekend_days' => $this->weekend_days,
            'late_deduction_rate' => $this->late_deduction_rate,
            'absent_deduction_rate' => $this->absent_deduction_rate,
            'overtime_rate' => $this->overtime_rate,
            'annual_leave_days' => $this->annual_leave_days,
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
