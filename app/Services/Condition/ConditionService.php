<?php

namespace App\Services\Condition;

use App\Models\Condition;
use App\Repositories\Condition\ConditionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Condition\ConditionResource;

class ConditionService
{
    /**
     * @var ConditionRepositoryInterface
     */
    private $repository;

    public function __construct(ConditionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = ConditionResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return ConditionResource::collection($results)->collection;
    }

    /**
     * @param Condition $condition
     * @return ConditionResource
     */
    public function show(Condition $condition): ConditionResource
    {
        return new ConditionResource($condition);
    }

    /**
     * @param array $params
     * @return Condition
     */
    public function create(array $params): Condition
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
