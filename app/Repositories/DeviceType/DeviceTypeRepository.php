<?php

namespace App\Repositories\DeviceType;

use App\Models\DeviceType;
use App\Repositories\BaseRepository;

class DeviceTypeRepository extends BaseRepository implements DeviceTypeRepositoryInterface
{
    public function __construct(DeviceType $model)
    {
        parent::__construct($model);
    }
}
