<?php

namespace App\Http\Requests\Department;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
        $id = $this->route('department');
        $id = is_object($id) ? $id->getKey() : $id;

        return [
            'department_name' => ['sometimes', 'string', 'max:100', Rule::unique('departments', 'department_name')->ignore($id)],
            'description' => ['nullable', 'string'],
            'parent_department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'is_active' => ['sometimes', 'boolean'],
            'code' => ['sometimes', 'string', 'max:255', Rule::unique('departments', 'code')->ignore($id)],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
