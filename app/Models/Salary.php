<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['employee_id', 'year', 'month', 'working_days', 'absent_days', 'holiday_days', 'basic_amount', 'deductions', 'bonus', 'overtime_amount', 'net_amount', 'status', 'paid_at', 'reviewed_by_user_id', 'reviewed_at', 'paid_by_user_id'])]
class Salary extends Model
{
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'working_days' => 'integer',
            'absent_days' => 'integer',
            'holiday_days' => 'integer',
            'basic_amount' => 'decimal:2',
            'deductions' => 'decimal:2',
            'bonus' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }
}
