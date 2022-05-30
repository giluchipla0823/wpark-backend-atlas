<?php

namespace App\Services\Application\Load;

use App\Models\Load;
use App\Repositories\Load\LoadRepositoryInterface;
use Illuminate\Http\Request;

class LoadService
{
    /**
     * @var LoadRepositoryInterface
     */
    private $repository;

    public function __construct(LoadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return void
     */
    public function confirmLeft(): void
    {
        // TODO: Actualizar campo "processed=1"

        // TODO: Realizar llamada api "Valencia ST8"

        // TODO: Realizar llamada api FreightVerify - CompoundExit

    }

    public function checkVehicles(array $params): array
    {
        return $this->repository->checkVehicles($params);
    }


    /**
     * @param array $params
     * @return Load
     */
    public function generate(array $params): Load
    {
        return $this->repository->generate($params);
    }
}
