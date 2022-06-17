<?php

namespace App\Repositories\Recirculation;

use App\Models\Recirculation;
use App\Repositories\BaseRepositoryInterface;

interface RecirculationRepositoryInterface extends BaseRepositoryInterface
{
    public function updateBack(Recirculation $recirculation): void;
}
