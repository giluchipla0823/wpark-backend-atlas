<?php

namespace App\Services\Application\Parking;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Parking\ParkingTypeResource;
use App\Models\ParkingType;
use App\Repositories\Parking\ParkingTypeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParkingTypeService
{
    /**
     * @var ParkingTypeRepositoryInterface
     */
    private $repository;

    public function __construct(ParkingTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = ParkingTypeResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return ParkingTypeResource::collection($results)->collection;
    }

    /**
     * @param ParkingType $parkingType
     * @return ParkingTypeResource
     */
    public function show(ParkingType $parkingType): ParkingTypeResource
    {
        return new ParkingTypeResource($parkingType);
    }

    /**
     * @param array $params
     * @return ParkingType
     */
    public function create(array $params): ParkingType
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
}
