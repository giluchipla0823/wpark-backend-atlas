<?php

namespace App\Services\Application\Rule;

use App\Helpers\Paginator\EloquentPaginator;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Rule\RuleResource;
use App\Models\Block;
use App\Models\Rule;
use App\Repositories\Rule\RuleRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        if ($results instanceof LengthAwarePaginator) {
            return (new EloquentPaginator($results, RuleResource::class))->collection();
        }

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
     * @throws Exception
     */
    public function create(array $params): Rule
    {
        return DB::transaction(function () use ($params) {
            $rule = $this->repository->create($params);

            return $this->createOrUpdateRelationship($rule, $params);
        });
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        DB::transaction(function() use($params, $id) {
            $modelAttributes = Rule::getModel()->getFillable();

            $this->repository->update(
                collect($params)->only($modelAttributes)->toArray(),
                $id
            );

            $rule = $this->repository->find($id);

            $this->createOrUpdateRelationship($rule, $params);
        });
    }

    /**
     * @param Rule $rule
     * @param array $params
     * @return Rule
     */
    private function createOrUpdateRelationship(Rule $rule, array $params): Rule
    {
        $presortingBlock = Block::where('presorting_default', 1)->first();

        if (! (bool) $params['is_group']) {
            $rule->blocks()->sync([$presortingBlock->id, $params['block_id']]);

            if ($rule->conditions()->count() > 0) {
                $currentConditionsIds = array_unique(
                    $rule->conditions()->get()
                        ->pluck('pivot.condition_id')
                        ->toArray()
                );

                $rule->conditions()->detach($currentConditionsIds);
            }

            $conditions = $params['conditions'];

            foreach ($conditions as $condition) {
                $rule->conditions()->attach($condition['condition_id'], [
                    'conditionable_type' => $condition['conditionable_type'],
                    'conditionable_id' => $condition['conditionable_id']
                ]);
            }
        } else {
            $rule->rules_groups()->sync($params['rules']);
            $rule->blocks()->sync($presortingBlock->id);
        }

        return $rule;
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

        $this->repository->update(['active' => $active], $rule->id);

        return $active;
    }
}
