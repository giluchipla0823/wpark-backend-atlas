<?php

namespace App\Services\Application\Hold;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Hold\HoldResource;
use App\Models\Hold;
use App\Repositories\Hold\HoldRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HoldService
{
    /**
     * @var HoldRepositoryInterface
     */
    private $repository;

    public function __construct(HoldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return HoldResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $results['data'] = HoldResource::collection($results['data'])->collection;;

        return collect($results);
    }

    /**
     * @param Hold $hold
     * @return HoldResource
     */
    public function show(Hold $hold): HoldResource
    {
        return new HoldResource($hold);
    }

    /**
     * @param array $params
     * @return Hold
     */
    public function create(array $params): Hold
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
     * @param Hold $hold
     * @return int
     */
    public function toggleActive(Hold $hold): int {
        $active = $hold->active ? 0 : 1;

        $this->update(['active' => $active], $hold->id);

        return $active;
    }
}
