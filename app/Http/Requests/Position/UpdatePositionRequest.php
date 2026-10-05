<?php

namespace App\Http\Requests\Position;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
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
        $id = $this->route('position');
        $id = is_object($id) ? $id->getKey() : $id;

        return [
            'position_name' => ['sometimes', 'string', 'max:100', Rule::unique('positions', 'position_name')->ignore($id)],
            'description' => ['nullable', 'string'],
            'code' => ['sometimes', 'string', 'max:255', Rule::unique('positions', 'code')->ignore($id)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'position_name.unique' => 'اسم المسمى الوظيفي مستخدم بالفعل.',
            'code.unique' => 'رمز المسمى الوظيفي مستخدم بالفعل.',
        ];
    }
}
