<?php

namespace App\Repositories\Device;

use App\Models\Device;
use App\Repositories\BaseRepositoryInterface;

interface DeviceRepositoryInterface extends BaseRepositoryInterface
{
    public function findOneByUuid(string $uuid): ?Device;
}
