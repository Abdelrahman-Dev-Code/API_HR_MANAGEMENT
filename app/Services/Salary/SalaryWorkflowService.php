<?php

namespace App\Services\Salary;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalaryWorkflowService
{
    public function createDraft(array $data): Salary
    {
        $data['net_amount'] = $this->calculateNetAmount($data);
        $data['status'] = 'pending';

        return Salary::query()->create($data);
    }

    public function calculateNetAmount(array $data, ?Salary $salary = null): string
    {
        $basicAmount = $this->toCents($data['basic_amount'] ?? $salary?->basic_amount ?? 0);
        $bonus = $this->toCents($data['bonus'] ?? $salary?->bonus ?? 0);
        $overtimeAmount = $this->toCents($data['overtime_amount'] ?? $salary?->overtime_amount ?? 0);
        $deductions = $this->toCents($data['deductions'] ?? $salary?->deductions ?? 0);
        $netAmount = $basicAmount + $bonus + $overtimeAmount - $deductions;

        if ($netAmount < 0) {
            throw ValidationException::withMessages([
                'deductions' => 'لا يمكن أن تتجاوز الخصومات إجمالي المستحقات.',
            ]);
        }

        return number_format($netAmount / 100, 2, '.', '');
    }

    public function assertDraft(Salary $salary): void
    {
        if ($salary->status !== 'pending' || $salary->paid_at !== null) {
            throw ValidationException::withMessages([
                'status' => 'لا يمكن تعديل أو حذف راتب تمت مراجعته أو دفعه.',
            ]);
        }
    }

    public function updateDraft(Salary $salary, array $data): Salary
    {
        return DB::transaction(function () use ($salary, $data): Salary {
            $lockedSalary = Salary::query()->lockForUpdate()->findOrFail($salary->id);
            $this->assertDraft($lockedSalary);
            $data['net_amount'] = $this->calculateNetAmount($data, $lockedSalary);
            $lockedSalary->update($data);

            return $lockedSalary->refresh()->load('employee');
        });
    }

    public function deleteDraft(Salary $salary): void
    {
        DB::transaction(function () use ($salary): void {
            $lockedSalary = Salary::query()->lockForUpdate()->findOrFail($salary->id);
            $this->assertDraft($lockedSalary);
            $lockedSalary->delete();
        });
    }

    public function decide(Salary $salary, User $reviewer, string $decision): Salary
    {
        return DB::transaction(function () use ($salary, $reviewer, $decision): Salary {
            $lockedSalary = Salary::query()->lockForUpdate()->findOrFail($salary->id);

            if ($lockedSalary->status !== 'pending' || $lockedSalary->paid_at !== null) {
                throw ValidationException::withMessages([
                    'status' => 'تمت مراجعة الراتب مسبقًا.',
                ]);
            }

            $lockedSalary->update([
                'status' => $decision,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            return $lockedSalary->refresh()->load('employee');
        });
    }

    public function markPaid(Salary $salary, User $payer): Salary
    {
        return DB::transaction(function () use ($salary, $payer): Salary {
            $lockedSalary = Salary::query()->lockForUpdate()->findOrFail($salary->id);

            if ($lockedSalary->status !== 'approved' || $lockedSalary->paid_at !== null) {
                throw ValidationException::withMessages([
                    'status' => 'لا يمكن دفع إلا راتب معتمد وغير مدفوع.',
                ]);
            }

            $lockedSalary->update([
                'paid_at' => now(),
                'paid_by_user_id' => $payer->id,
            ]);

            return $lockedSalary->refresh()->load('employee');
        });
    }

    private function toCents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
