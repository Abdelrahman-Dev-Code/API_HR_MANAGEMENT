<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use App\Models\Holiday;
use Illuminate\Validation\Validator;

class UpdateHolidayRequest extends FormRequest
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
        $id = $this->route('holiday');
        $id = is_object($id) ? $id->getKey() : $id;

        return [
            'holiday_name' => ['sometimes', 'string', 'max:100'],
            'holiday_date' => ['sometimes', 'date'],
            'description' => ['nullable', 'string'],
            'is_recurring' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'holiday_date.unique' => 'يوجد تسجيل لعطلة في هذا التاريخ بالفعل.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || ! $this->has('holiday_date')) {
                return;
            }

            $holiday = Holiday::query()->find($this->route('holiday'));

            if (! $holiday) {
                return;
            }

            $holidayExists = Holiday::query()
                ->whereDate('holiday_date', $this->input('holiday_date'))
                ->whereKeyNot($holiday->getKey())
                ->exists();

            if ($holidayExists) {
                $validator->errors()->add('holiday_date', 'يوجد تسجيل لعطلة في هذا التاريخ بالفعل.');
            }
        }];
    }
}
