<?php

namespace App\Http\Requests\Document;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'document_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'expiry_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'file.required' => 'يجب إرفاق ملف للمستند.',
            'file.mimes' => 'صيغة الملف غير مدعومة. الصيغ المقبولة: PDF أو JPG أو JPEG أو PNG.',
            'file.max' => 'يجب ألا يتجاوز حجم الملف 10 ميغابايت.',
            'employee_id.exists' => 'الموظف المحدد غير موجود.',
        ];
    }
}
