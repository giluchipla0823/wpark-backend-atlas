<?php

namespace App\Services\Application\Recirculation;

use App\Models\Recirculation;
use App\Repositories\Recirculation\RecirculationRepositoryInterface;

class RecirculationService
{
    /**
     * @var RecirculationRepositoryInterface
     */
    private $repository;

    public function __construct(
        RecirculationRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param Recirculation $recirculation
     * @return void
     */
    public function updateBack(Recirculation $recirculation) {
        $this->repository->updateBack($recirculation);
    }
}
