<?php

namespace App\Http\Requests\Salary;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StoreSalaryRequest extends FormRequest
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
                Rule::unique('salaries', 'employee_id')
                    ->where('year', $this->input('year'))
                    ->where('month', $this->input('month')),
            ],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'working_days' => ['sometimes', 'integer', 'min:0'],
            'absent_days' => ['sometimes', 'integer', 'min:0'],
            'holiday_days' => ['sometimes', 'integer', 'min:0'],
            'basic_amount' => ['required', 'numeric', 'min:0'],
            'deductions' => ['sometimes', 'numeric', 'min:0'],
            'bonus' => ['sometimes', 'numeric', 'min:0'],
            'overtime_amount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'يوجد راتب مسجل لهذا الموظف عن الشهر والسنة نفسيهما.',
        ];
    }
}
