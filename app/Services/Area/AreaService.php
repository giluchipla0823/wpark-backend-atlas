<?php

namespace App\Services\Area;

use App\Models\Area;
use App\Repositories\Area\AreaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Area\AreaResource;

class AreaService
{
    /**
     * @var AreaRepositoryInterface
     */
    private $repository;

    public function __construct(AreaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = AreaResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return AreaResource::collection($results)->collection;
    }

    /**
     * @param Area $area
     * @return AreaResource
     */
    public function show(Area $area): AreaResource
    {
        return new AreaResource($area);
    }

    /**
     * @param array $params
     * @return Area
     */
    public function create(array $params): Area
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
