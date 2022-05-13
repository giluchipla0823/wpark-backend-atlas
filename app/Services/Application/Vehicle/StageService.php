<?php

namespace App\Services\Application\Vehicle;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Vehicle\StageResource;
use App\Models\Stage;
use App\Repositories\Vehicle\StageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StageService
{
    /**
     * @var StageRepositoryInterface
     */
    private $repository;

    public function __construct(StageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = StageResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return StageResource::collection($results)->collection;
    }

    /**
     * @param Stage $stage
     * @return StageResource
     */
    public function show(Stage $stage): StageResource
    {
        return new StageResource($stage);
    }

    /**
     * @param array $params
     * @return Stage
     */
    public function create(array $params): Stage
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
