<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['setting_key', 'company_name', 'company_logo', 'timezone', 'currency_code', 'work_start_time', 'work_end_time', 'day_shift_start_time', 'day_shift_end_time', 'night_shift_start_time', 'night_shift_end_time', 'weekend_days', 'late_deduction_rate', 'absent_deduction_rate', 'overtime_rate', 'annual_leave_days'])]
class Setting extends Model
{
    protected function casts(): array
    {
        return [
            'weekend_days' => 'array',
            'late_deduction_rate' => 'decimal:2',
            'absent_deduction_rate' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'annual_leave_days' => 'integer',
        ];
    }
}
