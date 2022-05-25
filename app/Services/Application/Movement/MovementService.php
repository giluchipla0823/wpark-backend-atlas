<?php

namespace App\Services\Application\Movement;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Movement\MovementResource;
use App\Http\Resources\Movement\MovementDatatablesResource;
use App\Models\Movement;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class MovementService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $repository;

    public function __construct(MovementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return MovementResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = MovementDatatablesResource::collection($results['data']);

        $results['data'] = $resource->collection->toArray();

        return collect($results);
    }

    /**
     * @param Movement $movement
     * @return MovementResource
     */
    public function show(Movement $movement): MovementResource
    {
        $movement->load(QueryParamsHelper::getIncludesParamFromRequest());

        return new MovementResource($movement);
    }

    /**
     * @param array $params
     * @return Movement
     */
    public function create(array $params): Movement
    {

        $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();

        $params['category'] = $vehicle->shippingRule->name;
        $params['dt_start'] = Carbon::now();

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
