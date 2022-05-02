<?php

namespace App\Services\Vehicle;

use App\Http\Resources\Row\RowVehicleResource;
use App\Models\Row;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Http\Resources\Vehicle\VehicleStateResource;
use App\Http\Resources\Vehicle\InfoVehicleResource;

class VehicleService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $repository;

    public function __construct(VehicleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = VehicleResource::collection($data);

            $results->put('data', $resource->collection->toArray());

            return $results;
        }

        return VehicleResource::collection($results)->collection;
    }

    /**
     * @param Vehicle $vehicle
     * @return VehicleResource
     */
    public function show(Vehicle $vehicle): VehicleResource
    {
        return new VehicleResource($vehicle);
    }

    /**
     * @param array $params
     * @return Vehicle
     */
    public function create(array $params): Vehicle
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

    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }

    /**
     * @param Row $row
     * @return Collection
     */
    public function findAllByRow(Row $row): Collection
    {
        $results = $this->repository->findAllByRow($row);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = RowVehicleResource::collection($data);

            $results->put('data', $resource->collection->toArray());

            return $results;
        }

        return RowVehicleResource::collection($results)->collection;
    }

    /**
     * @param Vehicle $vehicle
     * @return InfoVehicleResource
     */
    public function detail(Vehicle $vehicle): InfoVehicleResource
    {
        return new InfoVehicleResource($vehicle);
    }

    /**
     * @param State $state
     * @return Collection
     */
    public function findAllByState(State $state): Collection
    {
        $results = $this->repository->findAllByState($state);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = VehicleStateResource::collection($data);

            $results->put('data', $resource->collection->toArray());

            return $results;
        }

        return VehicleStateResource::collection($results)->collection;
    }
}
