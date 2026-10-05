<?php

namespace App\Http\Requests\Department;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
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
            'department_name' => ['required', 'string', 'max:100', 'unique:departments,department_name'],
            'description' => ['nullable', 'string'],
            'parent_department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'is_active' => ['sometimes', 'boolean'],
            'code' => ['required', 'string', 'max:255', 'unique:departments,code'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'department_name.unique' => 'اسم القسم مستخدم بالفعل.',
            'code.unique' => 'كود القسم مستخدم بالفعل.',
        ];
    }
}
