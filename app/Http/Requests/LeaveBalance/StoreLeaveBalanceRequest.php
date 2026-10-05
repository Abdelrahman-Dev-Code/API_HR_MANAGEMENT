<?php

namespace App\Http\Requests\LeaveBalance;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveBalanceRequest extends FormRequest
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
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
                Rule::unique('leave_balances', 'employee_id')
                    ->where('year', $this->input('year'))
                    ->where('leave_type', $this->input('leave_type')),
            ],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'leave_type' => ['required', Rule::in(['sick', 'casual', 'earned', 'maternity', 'paternity', 'bereavement'])],
            'allocated_days' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'يوجد رصيد مسجل لهذا الموظف والسنة ونوع الإجازة.',
        ];
    }
}
