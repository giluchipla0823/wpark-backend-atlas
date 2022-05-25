<?php

namespace App\Services\Application\Design;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Rule\RuleResource;
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

        return DesignResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = DesignResource::collection($results['data']);

        $results['data'] = $resource->collection;

        return collect($results);
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
