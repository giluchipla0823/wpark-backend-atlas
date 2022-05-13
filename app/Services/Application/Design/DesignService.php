<?php

namespace App\Services\Application\Design;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Design\DesignResource;
use App\Models\Design;
use App\Repositories\Design\DesignRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DesignService
{
    /**
     * @var DesignRepositoryInterface
     */
    private $repository;

    public function __construct(DesignRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = DesignResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return DesignResource::collection($results)->collection;
    }

    /**
     * @param Design $design
     * @return DesignResource
     */
    public function show(Design $design): DesignResource
    {
        return new DesignResource($design);
    }

    /**
     * @param array $params
     * @return Design
     */
    public function create(array $params): Design
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
