<?php

namespace App\Services\Application\Condition;

use App\Exceptions\owner\BadRequestException;
use Exception;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Helpers\ClassHelper;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Condition\ConditionResource;
use App\Repositories\Condition\ConditionRepositoryInterface;

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

    /**
     * @param Request $request
     * @return Collection
     */
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
            throw new BadRequestException("La condición {$condition->name} no tiene un modelo especificado.");
        }

        if (!class_exists($condition->model)) {
            throw new BadRequestException("El modelo {$condition->model} no existe y por ende no tiene información al respecto para la condición {$condition->name}.");
        }

        /* @var Model $model */
        $model = (new $condition->model);

        $query = $model->query();

        if (ClassHelper::hasConstant($condition->model, 'UNKNOWN_ID')) {
            $query = $query->whereNotIn("id", [$condition->model::UNKNOWN_ID]);
        }

        return $query->get();
    }
}
