<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\SanitizingFormRequest as FormRequest;

class UpdateOrganizationRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'company_name' => ['sometimes', 'required', 'string', 'max:100'],
            'company_logo' => ['sometimes', 'nullable', 'string', 'max:255'],
            'timezone' => ['sometimes', 'required', 'timezone'],
            'currency_code' => ['sometimes', 'required', 'regex:/^[A-Z]{3}$/'],
            'work_start_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'work_end_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'day_shift_start_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'day_shift_end_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'night_shift_start_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'night_shift_end_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'weekend_days' => ['sometimes', 'required', 'array', 'min:1'],
            'weekend_days.*' => ['string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'late_deduction_rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'absent_deduction_rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'overtime_rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'annual_leave_days' => ['sometimes', 'required', 'integer', 'min:0', 'max:366'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'اسم المؤسسة مطلوب.',
            'timezone.timezone' => 'المنطقة الزمنية غير صحيحة.',
            'currency_code.regex' => 'رمز العملة يجب أن يتكون من ثلاثة أحرف إنجليزية كبيرة.',
            'weekend_days.*.in' => 'أحد أيام العطلة المحددة غير صحيح.',
            'annual_leave_days.max' => 'عدد أيام الإجازة السنوية يتجاوز الحد المنطقي.',
        ];
    }
}
