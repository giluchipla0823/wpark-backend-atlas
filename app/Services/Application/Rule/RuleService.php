<?php

namespace App\Services\Application\Rule;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Rule\RuleResource;
use App\Models\Rule;
use App\Repositories\Rule\RuleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RuleService
{
    /**
     * @var RuleRepositoryInterface
     */
    private $repository;

    public function __construct(RuleRepositoryInterface $repository)
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

        return RuleResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = RuleResource::collection($results['data']);

        $results['data'] = $resource->collection;

        return collect($results);
    }

    /**
     * @param Rule $rule
     * @return RuleResource
     */
    public function show(Rule $rule): RuleResource
    {
        $rule->load(QueryParamsHelper::getIncludesParamFromRequest());

        return new RuleResource($rule);
    }

    /**
     * @param array $params
     * @return Rule
     */
    public function create(array $params): Rule
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
     * @param Rule $rule
     * @return int
     */
    public function toggleActive(Rule $rule): int {
        $active = $rule->active ? 0 : 1;

        $this->update(['active' => $active], $rule->id);

        return $active;
    }
}
