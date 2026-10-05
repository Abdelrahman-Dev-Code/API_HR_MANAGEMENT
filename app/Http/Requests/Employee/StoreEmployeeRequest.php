<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('employees', 'user_id')],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'employee_code' => ['required', 'string', 'max:255', 'unique:employees,employee_code'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'profile_photo' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'terminated'])],
            'work_shift' => ['sometimes', Rule::in(['day', 'night'])],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'هذا المستخدم مرتبط بموظف بالفعل.',
            'employee_code.unique' => 'الرقم الوظيفي مستخدم بالفعل.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'birth_date.before' => 'يجب أن يسبق تاريخ الميلاد تاريخ اليوم.',
        ];
    }
}
