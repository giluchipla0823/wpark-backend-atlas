<?php

namespace App\Services\Application\Carrier;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Carrier\CarrierResource;
use App\Models\Carrier;
use App\Repositories\Carrier\CarrierRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CarrierService
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $repository;

    public function __construct(
        CarrierRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return CarrierResource::collection($results)->collection;
    }

    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $results['data'] = CarrierResource::collection($results['data'])->collection;;

        return collect($results);
    }

    /**
     * @param Carrier $carrier
     * @return CarrierResource
     */
    public function show(Carrier $carrier): CarrierResource
    {
        return new CarrierResource($carrier);
    }

    /**
     * @param array $params
     * @return Carrier
     */
    public function create(array $params): Carrier
    {
        return $this->repository->create($params);
    }


    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->repository->update($params, $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }
}
