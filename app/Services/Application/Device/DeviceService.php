<?php

namespace App\Services\Application\Device;

use App\Exceptions\owner\NotFoundException;
use App\Models\Device;
use App\Repositories\Device\DeviceRepositoryInterface;

class DeviceService
{
    /**
     * @var DeviceRepositoryInterface
     */
    private $repository;

    public function __construct(
        DeviceRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Registro de nuevo dispositivo.
     *
     * @param array $params
     * @return Device
     */
    public function store(array $params): Device
    {
        return $this->repository->create($params);
    }

    /**
     * Buscar dispositivo por uuid.
     *
     * @param string $uuid
     * @return Device|null
     */
    public function findOneByUuid(string $uuid): ?Device
    {
        return $this->repository->findOneByUuid($uuid);
    }

    /**
     * Buscar dispositivo por uuid y en caso de no encontrarlo lanzar excepción.
     *
     * @param string $uuid
     * @return Device
     * @throws NotFoundException
     */
    public function findOneOrFailByUuid(string $uuid): Device
    {
        $device = $this->findOneByUuid($uuid);

        if (null === $device) {
            throw new NotFoundException("No se encontró información del dispositivo con el uuid especificado.");
        }

        return $device;
    }
}
