<?php

namespace App\Services\Application\Compound;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Compound\CompoundResource;
use App\Models\Compound;
use App\Repositories\Compound\CompoundRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CompoundService
{
    /**
     * @var CompoundRepositoryInterface
     */
    private $repository;

    public function __construct(CompoundRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = CompoundResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return CompoundResource::collection($results)->collection;
    }

    /**
     * @param Compound $compound
     * @return CompoundResource
     */
    public function show(Compound $compound): CompoundResource
    {
        return new CompoundResource($compound);
    }

    /**
     * @param array $params
     * @return Compound
     */
    public function create(array $params): Compound
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
