<?php

namespace App\Http\Requests\Position;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StorePositionRequest extends FormRequest
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
            'position_name' => ['required', 'string', 'max:100', 'unique:positions,position_name'],
            'description' => ['nullable', 'string'],
            'code' => ['required', 'string', 'max:255', 'unique:positions,code'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'position_name.required' => 'اسم المسمى الوظيفي مطلوب.',
            'position_name.unique' => 'اسم المسمى الوظيفي مستخدم بالفعل.',
            'code.required' => 'رمز المسمى الوظيفي مطلوب.',
            'code.unique' => 'رمز المسمى الوظيفي مستخدم بالفعل.',
        ];
    }
}
