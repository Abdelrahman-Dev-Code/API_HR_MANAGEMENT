<?php

namespace App\Services\Leave;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Setting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeaveWorkflowService
{
    public function submit(array $data): Leave
    {
        return DB::transaction(function () use ($data): Leave {
            Employee::query()->findOrFail($data['employee_id']);
            $daysByYear = $this->workingDaysByYear($data['start_date'], $data['end_date']);

            return Leave::query()->create([
                ...$data,
                'status' => 'pending',
                'total_days' => array_sum($daysByYear),
            ])->load(['employee', 'approver']);
        });
    }

    public function updatePending(Leave $leave, array $data): Leave
    {
        return DB::transaction(function () use ($leave, $data): Leave {
            Employee::query()->whereKey($leave->employee_id)->lockForUpdate()->firstOrFail();
            $lockedLeave = Leave::query()->lockForUpdate()->findOrFail($leave->id);

            if ($lockedLeave->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'لا يمكن تعديل طلب تمت مراجعته.',
                ]);
            }

            $attributes = [...$data];
            $startDate = $attributes['start_date'] ?? $lockedLeave->start_date->toDateString();
            $endDate = $attributes['end_date'] ?? $lockedLeave->end_date->toDateString();
            $attributes['total_days'] = array_sum($this->workingDaysByYear($startDate, $endDate));

            $lockedLeave->update($attributes);

            return $lockedLeave->refresh()->load(['employee', 'approver']);
        });
    }

    public function decide(Leave $leave, User $approver, string $decision, ?string $rejectionReason = null): Leave
    {
        return DB::transaction(function () use ($leave, $approver, $decision, $rejectionReason): Leave {
            Employee::query()->whereKey($leave->employee_id)->lockForUpdate()->firstOrFail();
            $lockedLeave = Leave::query()->lockForUpdate()->findOrFail($leave->id);

            if ($lockedLeave->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'تمت مراجعة طلب الإجازة مسبقًا.',
                ]);
            }

            $approverEmployee = $approver->employee()->first();

            if (! $approverEmployee) {
                throw ValidationException::withMessages([
                    'approver' => 'يجب ربط حساب المعتمد بملف موظف قبل مراجعة الإجازات.',
                ]);
            }

            $daysByYear = $this->workingDaysByYear(
                $lockedLeave->start_date->toDateString(),
                $lockedLeave->end_date->toDateString(),
            );

            if ($decision === 'approved') {
                $overlapsApprovedLeave = Leave::query()
                    ->where('employee_id', $lockedLeave->employee_id)
                    ->where($lockedLeave->getKeyName(), '<>', $lockedLeave->getKey())
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $lockedLeave->end_date)
                    ->whereDate('end_date', '>=', $lockedLeave->start_date)
                    ->exists();

                if ($overlapsApprovedLeave) {
                    throw ValidationException::withMessages([
                        'start_date' => 'يتداخل هذا الطلب مع إجازة معتمدة للموظف.',
                    ]);
                }

                if ($lockedLeave->leave_type !== 'unpaid') {
                    $this->reserveLeaveBalance($lockedLeave, $daysByYear);
                }
            }

            $lockedLeave->update([
                'status' => $decision,
                'approved_by' => $approverEmployee->id,
                'rejection_reason' => $decision === 'rejected' ? $rejectionReason : null,
                'total_days' => array_sum($daysByYear),
            ]);

            return $lockedLeave->refresh()->load(['employee', 'approver']);
        });
    }

    /** @return array<int, int> */
    private function workingDaysByYear(string $startDate, string $endDate): array
    {
        $start = CarbonImmutable::parse($startDate)->startOfDay();
        $end = CarbonImmutable::parse($endDate)->startOfDay();
        $company = Setting::query()->where('setting_key', 'company')->first();
        $weekendDays = $company?->weekend_days ?? ['Friday', 'Saturday'];
        $holidays = Holiday::query()
            ->whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->orWhere('is_recurring', true)
            ->get(['holiday_date', 'is_recurring']);
        $holidayDates = $holidays
            ->reject(fn (Holiday $holiday): bool => $holiday->is_recurring)
            ->map(fn (Holiday $holiday): string => $holiday->holiday_date->toDateString())
            ->flip();
        $recurringHolidayDates = $holidays
            ->filter(fn (Holiday $holiday): bool => $holiday->is_recurring)
            ->map(fn (Holiday $holiday): string => $holiday->holiday_date->format('m-d'))
            ->flip();
        $daysByYear = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $dateKey = $date->toDateString();

            if (
                in_array($date->format('l'), $weekendDays, true)
                || $holidayDates->has($dateKey)
                || $recurringHolidayDates->has($date->format('m-d'))
            ) {
                continue;
            }

            $year = (int) $date->format('Y');
            $daysByYear[$year] = ($daysByYear[$year] ?? 0) + 1;
        }

        if ($daysByYear === []) {
            throw ValidationException::withMessages([
                'start_date' => 'الفترة المحددة لا تحتوي على أيام عمل.',
            ]);
        }

        return $daysByYear;
    }

    /** @param array<int, int> $daysByYear */
    private function reserveLeaveBalance(Leave $leave, array $daysByYear): void
    {
        foreach ($daysByYear as $year => $days) {
            $balance = LeaveBalance::query()
                ->where('employee_id', $leave->employee_id)
                ->where('year', $year)
                ->where('leave_type', $leave->leave_type)
                ->lockForUpdate()
                ->first();

            if (! $balance || $balance->used_days + $days > $balance->allocated_days) {
                throw ValidationException::withMessages([
                    'leave_balance' => "رصيد الإجازة غير كافٍ لسنة {$year}.",
                ]);
            }

            $balance->increment('used_days', $days);
        }
    }

    public function deletePending(Leave $leave): void
    {
        DB::transaction(function () use ($leave): void {
            Employee::query()->whereKey($leave->employee_id)->lockForUpdate()->firstOrFail();
            $lockedLeave = Leave::query()->lockForUpdate()->findOrFail($leave->id);

            if ($lockedLeave->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'لا يمكن حذف طلب إجازة تمت مراجعته.',
                ]);
            }

            $lockedLeave->delete();
        });
    }
}
