<?php

namespace App\Services\Application\DeviceType;

use App\Http\Resources\DeviceType\DeviceTypeResource;
use App\Repositories\DeviceType\DeviceTypeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DeviceTypeService
{
    /**
     * @var DeviceTypeRepositoryInterface
     */
    private $repository;

    public function __construct(
        DeviceTypeRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function findAll(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return DeviceTypeResource::collection($results)->collection;
    }
}
