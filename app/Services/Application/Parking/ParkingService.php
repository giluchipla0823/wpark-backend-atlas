<?php

namespace App\Services\Application\Parking;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Parking\ParkingResource;
use App\Models\Parking;
use App\Repositories\Parking\ParkingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParkingService
{
    /**
     * @var ParkingRepositoryInterface
     */
    private $repository;

    public function __construct(ParkingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = ParkingResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return ParkingResource::collection($results)->collection;
    }

    /**
     * @param Parking $parking
     * @return ParkingResource
     */
    public function show(Parking $parking): ParkingResource
    {
        return new ParkingResource($parking);
    }

    /**
     * @param array $params
     * @return Parking
     */
    public function create(array $params): Parking
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
     * @param Parking $parking
     * @return int
     */
    public function toggleActive(Parking $parking): int {
        $active = $parking->active ? 0 : 1;

        $this->update(['active' => $active], $parking->id);

        return $active;
    }
}
