<?php

namespace App\Http\Requests\Leave;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use App\Models\Leave;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateLeaveRequest extends FormRequest
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
            'leave_type' => ['sometimes', Rule::in(['sick', 'casual', 'earned', 'maternity', 'paternity', 'bereavement', 'unpaid'])],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'reason' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $leave = $this->route('leave');
            $leave = $leave instanceof Leave ? $leave : Leave::query()->find($leave);

            if (! $leave) {
                return;
            }

            $startDate = CarbonImmutable::parse($this->input('start_date', $leave->start_date->toDateString()));
            $endDate = CarbonImmutable::parse($this->input('end_date', $leave->end_date->toDateString()));

            if ($endDate->lessThan($startDate)) {
                $validator->errors()->add('end_date', 'يجب أن يكون تاريخ النهاية مساويًا أو لاحقًا لتاريخ البداية.');
            }
        }];
    }
}
