<?php

namespace App\Services\Condition;

use Exception;
use App\Models\Condition;
use App\Repositories\Condition\ConditionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Condition\ConditionResource;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @param Condition $condition
     * @return Collection
     * @throws Exception
     */
    public function getModelDataByCondition(Condition $condition): Collection
    {
        if (!$condition->model) {
            throw new Exception(
                "La condición {$condition->name} no tiene un modelo especificado.",
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!class_exists($condition->model)) {
            throw new Exception(
                "El modelo {$condition->model} no existe y por ende no tiene información al respecto para la condición {$condition->name}.",
                Response::HTTP_BAD_REQUEST
            );
        }

        return (new $condition->model)->query()->get();
    }
}
