<?php

namespace App\Repositories\Device;

use App\Models\Device;
use App\Repositories\BaseRepository;

class DeviceRepository extends BaseRepository implements DeviceRepositoryInterface
{
    public function __construct(Device $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $uuid
     * @return Device|null
     */
    public function findOneByUuid(string $uuid): ?Device
    {
        return $this->model->query()->where("uuid", $uuid)->first();
    }
}
