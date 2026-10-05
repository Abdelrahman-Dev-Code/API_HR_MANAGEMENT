<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\Shared\BaseRepository;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Setting());
    }

    public function company(): Setting
    {
        return $this->model->newQuery()->firstOrCreate(
            ['setting_key' => 'company'],
            [
                'company_name' => 'My Company',
                'timezone' => 'Asia/Riyadh',
                'currency_code' => 'SAR',
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'day_shift_start_time' => '08:00:00',
                'day_shift_end_time' => '17:00:00',
                'night_shift_start_time' => '22:00:00',
                'night_shift_end_time' => '06:00:00',
                'weekend_days' => ['Friday', 'Saturday'],
                'late_deduction_rate' => 0.5,
                'absent_deduction_rate' => 1.0,
                'overtime_rate' => 1.5,
                'annual_leave_days' => 21,
            ],
        );
    }

    public function updateCompany(array $data): Setting
    {
        $company = $this->company();
        $company->update($data);

        return $company->refresh();
    }
}
