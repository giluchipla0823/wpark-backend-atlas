<?php

namespace App\Services\Application\State;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\State\StateResource;
use App\Models\State;
use App\Repositories\State\StateRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StateService
{
    /**
     * @var StateRepositoryInterface
     */
    private $repository;

    public function __construct(StateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = StateResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return StateResource::collection($results)->collection;
    }

    /**
     * @param State $state
     * @return StateResource
     */
    public function show(State $state): StateResource
    {
        return new StateResource($state);
    }

    /**
     * @param array $params
     * @return State
     */
    public function create(array $params): State
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
