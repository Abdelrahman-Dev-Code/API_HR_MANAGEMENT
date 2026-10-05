<?php

namespace App\Http\Requests\Salary;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use App\Models\Salary;
use Illuminate\Validation\Validator;

class UpdateSalaryRequest extends FormRequest
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
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'working_days' => ['sometimes', 'integer', 'min:0'],
            'absent_days' => ['sometimes', 'integer', 'min:0'],
            'holiday_days' => ['sometimes', 'integer', 'min:0'],
            'basic_amount' => ['sometimes', 'numeric', 'min:0'],
            'deductions' => ['sometimes', 'numeric', 'min:0'],
            'bonus' => ['sometimes', 'numeric', 'min:0'],
            'overtime_amount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $salary = Salary::query()->find($this->route('salary'));

            if (! $salary) {
                return;
            }

            $duplicateSalary = Salary::query()
                ->where('employee_id', $this->integer('employee_id', $salary->employee_id))
                ->where('year', $this->integer('year', $salary->year))
                ->where('month', $this->integer('month', $salary->month))
                ->whereKeyNot($salary->getKey())
                ->exists();

            if ($duplicateSalary) {
                $validator->errors()->add('month', 'يوجد راتب مسجل لهذا الموظف عن الشهر والسنة نفسيهما.');
            }
        }];
    }
}
