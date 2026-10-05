<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        $id = $this->route('role');
        $id = is_object($id) ? $id->getKey() : $id;

        return [
            'role_name' => ['sometimes', 'string', 'max:50', Rule::unique('roles', 'role_name')->ignore($id)],
            'display_name' => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'role_name.unique' => 'اسم الدور مستخدم بالفعل.',
            'permission_ids.array' => 'حقل معرفات الأذونات يجب أن يكون مصفوفة.',
            'permission_ids.*.integer' => 'كل معرف إذن يجب أن يكون عددًا صحيحًا.',
            'permission_ids.*.exists' => 'أحد معرفات الأذونات المحددة غير موجودة في قاعدة البيانات.',
        ];
    }
}
