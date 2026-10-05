<?php

namespace App\Http\Requests\LeaveBalance;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use App\Models\LeaveBalance;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateLeaveBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'year' => ['sometimes', 'integer', 'between:2000,2100'],
            'leave_type' => ['sometimes', Rule::in(['sick', 'casual', 'earned', 'maternity', 'paternity', 'bereavement'])],
            'allocated_days' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $balance = LeaveBalance::query()->find($this->route('leave_balance'));

            if (! $balance) {
                return;
            }

            $employeeId = (int) $this->input('employee_id', $balance->employee_id);
            $year = (int) $this->input('year', $balance->year);
            $leaveType = (string) $this->input('leave_type', $balance->leave_type);
            $allocatedDays = (int) $this->input('allocated_days', $balance->allocated_days);

            if ($allocatedDays < $balance->used_days) {
                $validator->errors()->add('allocated_days', 'لا يمكن أن يقل الرصيد المخصص عن الأيام المستخدمة.');
            }

            $duplicateBalance = LeaveBalance::query()
                ->where('employee_id', $employeeId)
                ->where('year', $year)
                ->where('leave_type', $leaveType)
                ->whereKeyNot($balance->getKey())
                ->exists();

            if ($duplicateBalance) {
                $validator->errors()->add('leave_type', 'يوجد رصيد مسجل لهذا الموظف والسنة ونوع الإجازة.');
            }
        }];
    }
}
