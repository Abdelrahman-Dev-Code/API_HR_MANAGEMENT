<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'shift' => ['required', Rule::in(['day', 'night'])],
            'check_in_at' => ['nullable', 'required_with:check_out_at', 'date'],
            'check_out_at' => ['nullable', 'date', 'after:check_in_at'],
            'status' => ['sometimes', Rule::in(['present', 'absent', 'leave', 'holiday', 'late'])],
            'late_minutes' => ['sometimes', 'integer', 'min:0'],
            'overtime_minutes' => ['sometimes', 'integer', 'min:0'],
            'source' => ['sometimes', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
            'location' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'يوجد سجل حضور لهذا الموظف في التاريخ والوردية نفسيهما.',
            'check_in_at.required_with' => 'وقت الحضور مطلوب عند إدخال وقت الانصراف.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $employeeShift = Employee::query()
                ->whereKey($this->integer('employee_id'))
                ->value('work_shift');

            if ($employeeShift !== $this->input('shift')) {
                $validator->errors()->add('shift', 'الوردية لا تطابق الوردية الأساسية للموظف.');
            }

            $duplicateAttendance = Attendance::query()
                ->where('employee_id', $this->integer('employee_id'))
                ->whereDate('attendance_date', $this->input('attendance_date'))
                ->where('shift', $this->input('shift'))
                ->exists();

            if ($duplicateAttendance) {
                $validator->errors()->add('attendance_date', 'يوجد سجل حضور لهذا الموظف في التاريخ والوردية نفسيهما.');
            }

            $checkIn = $this->input('check_in_at');

            if ($checkIn && CarbonImmutable::parse($checkIn)->toDateString() !== $this->input('attendance_date')) {
                $validator->errors()->add('check_in_at', 'يجب أن يقع الدخول في تاريخ بداية الوردية.');
            }
        }];
    }
}
