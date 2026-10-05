<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreRoleRequest extends FormRequest
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
            'role_name' => ['required', 'string', 'max:50', 'unique:roles,role_name'],
            'display_name' => ['required', 'string', 'max:100'],
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
            'role_name.required' => 'حقل اسم الدور مطلوب.',
            'role_name.string' => 'حقل اسم الدور يجب أن يكون نصًا.',
            'role_name.max' => 'حقل اسم الدور يجب ألا يتجاوز 50 حرفًا.',
            'role_name.unique' => 'اسم الدور مستخدم بالفعل.',
            'display_name.required' => 'حقل الاسم المعروض مطلوب.',
            'display_name.string' => 'حقل الاسم المعروض يجب أن يكون نصًا.',
            'display_name.max' => 'حقل الاسم المعروض يجب ألا يتجاوز 100 حرفًا.',
            'description.string' => 'حقل الوصف يجب أن يكون نصًا.',
            'is_active.boolean' => 'حقل الحالة النشطة يجب أن يكون صحيحًا أو خطأً.',
            'permission_ids.array' => 'حقل معرفات الأذونات يجب أن يكون مصفوفة.',
            'permission_ids.*.integer' => 'كل معرف إذن يجب أن يكون عددًا صحيحًا.',
            'permission_ids.*.exists' => 'أحد معرفات الأذونات المحددة غير موجودة في قاعدة البيانات.',
        ];
    }
}
