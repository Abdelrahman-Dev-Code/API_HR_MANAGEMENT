<?php

namespace App\Repositories\Holiday;

use App\Models\Holiday;
use App\Repositories\Shared\BaseRepository;

class HolidayRepository extends BaseRepository implements HolidayRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Holiday());
    }
}
