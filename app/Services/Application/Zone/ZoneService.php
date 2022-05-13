<?php

namespace App\Services\Application\Zone;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Zone\ZoneResource;
use App\Models\Zone;
use App\Repositories\Zone\ZoneRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ZoneService
{
    /**
     * @var ZoneRepositoryInterface
     */
    private $repository;

    public function __construct(ZoneRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = ZoneResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return ZoneResource::collection($results)->collection;
    }

    /**
     * @param Zone $zone
     * @return ZoneResource
     */
    public function show(Zone $zone): ZoneResource
    {
        return new ZoneResource($zone);
    }

    /**
     * @param array $params
     * @return Zone
     */
    public function create(array $params): Zone
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
