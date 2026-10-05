<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\Contracts\RepositoryInterface;

interface SettingRepositoryInterface extends RepositoryInterface
{
	public function company(): Setting;

	public function updateCompany(array $data): Setting;
}
