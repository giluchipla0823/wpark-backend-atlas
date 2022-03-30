<?php

namespace App\Services\Slot;

use App\Models\Slot;
use App\Repositories\Slot\SlotRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Slot\SlotResource;

class SlotService
{
    /**
     * @var SlotRepositoryInterface
     */
    private $repository;

    public function __construct(SlotRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = SlotResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return SlotResource::collection($results)->collection;
    }

    /**
     * @param Slot $area
     * @return SlotResource
     */
    public function show(Slot $area): SlotResource
    {
        return new SlotResource($area);
    }

    /**
     * @param array $params
     * @return Slot
     */
    public function create(array $params): Slot
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
