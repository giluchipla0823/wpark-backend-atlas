<?php

namespace App\Repositories\Load;

use App\Models\Load;
use App\Repositories\BaseRepositoryInterface;

interface LoadRepositoryInterface extends BaseRepositoryInterface
{
    public function generate(array $params): Load;
    public function checkVehicles(array $params): array;

}
