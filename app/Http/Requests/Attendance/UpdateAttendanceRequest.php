<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\SanitizingFormRequest as FormRequest;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends FormRequest
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
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'attendance_date' => ['sometimes', 'date'],
            'shift' => ['sometimes', Rule::in(['day', 'night'])],
            'check_in_at' => ['nullable', 'required_with:check_out_at', 'date'],
            'check_out_at' => ['nullable', 'date'],
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

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $attendance = Attendance::query()->find($this->route('attendance'));

            if (! $attendance) {
                return;
            }

            $employeeId = $this->integer('employee_id', $attendance->employee_id);
            $shift = $this->input('shift', $attendance->shift);
            $employeeShift = Employee::query()->whereKey($employeeId)->value('work_shift');

            if ($employeeShift !== $shift) {
                $validator->errors()->add('shift', 'الوردية لا تطابق الوردية الأساسية للموظف.');
            }

            $attendanceDate = $this->input('attendance_date', $attendance->attendance_date->toDateString());
            $checkIn = $this->input('check_in_at', $attendance->check_in_at?->toISOString());
            $checkOut = $this->input('check_out_at', $attendance->check_out_at?->toISOString());

            $duplicateAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->whereDate('attendance_date', $attendanceDate)
                ->where('shift', $shift)
                ->whereKeyNot($attendance->getKey())
                ->exists();

            if ($duplicateAttendance) {
                $validator->errors()->add('attendance_date', 'يوجد سجل حضور لهذا الموظف في التاريخ والوردية نفسيهما.');
            }

            if ($checkOut && ! $checkIn) {
                $validator->errors()->add('check_in_at', 'وقت الحضور مطلوب عند إدخال وقت الانصراف.');
            }

            if ($checkIn && CarbonImmutable::parse($checkIn)->toDateString() !== $attendanceDate) {
                $validator->errors()->add('check_in_at', 'يجب أن يقع الدخول في تاريخ بداية الوردية.');
            }

            if ($checkIn && $checkOut && ! CarbonImmutable::parse($checkOut)->greaterThan(CarbonImmutable::parse($checkIn))) {
                $validator->errors()->add('check_out_at', 'يجب أن يكون الخروج بعد وقت الدخول.');
            }
        }];
    }
}
