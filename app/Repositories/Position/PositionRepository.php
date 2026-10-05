<?php

namespace App\Repositories\Position;

use App\Models\Position;
use App\Repositories\Shared\BaseRepository;

class PositionRepository extends BaseRepository implements PositionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Position());
    }
}
