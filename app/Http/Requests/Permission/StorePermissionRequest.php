<?php

namespace App\Http\Requests\Permission;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;

class StorePermissionRequest extends FormRequest
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
            'permission_name' => ['required', 'string', 'max:50', 'unique:permissions,permission_name'],
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'group' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * Return clear Arabic messages for permission creation validation.
     *
     * @author GitHub Copilot
     * @created 2026-09-28
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permission_name.required' => 'اسم الصلاحية مطلوب.',
            'permission_name.string' => 'يجب أن يكون اسم الصلاحية نصًا.',
            'permission_name.max' => 'يجب ألا يتجاوز اسم الصلاحية 50 حرفًا.',
            'permission_name.unique' => 'اسم الصلاحية مستخدم بالفعل.',
            'display_name.required' => 'الاسم المعروض للصلاحية مطلوب.',
            'display_name.string' => 'يجب أن يكون الاسم المعروض للصلاحية نصًا.',
            'display_name.max' => 'يجب ألا يتجاوز الاسم المعروض للصلاحية 100 حرف.',
            'description.string' => 'يجب أن يكون وصف الصلاحية نصًا.',
            'group.required' => 'مجموعة الصلاحية مطلوبة.',
            'group.string' => 'يجب أن يكون اسم المجموعة نصًا.',
            'group.max' => 'يجب ألا يتجاوز اسم المجموعة 50 حرفًا.',
        ];
    }
}
