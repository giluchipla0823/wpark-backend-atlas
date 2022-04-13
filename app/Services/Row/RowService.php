<?php

namespace App\Services\Row;

use App\Models\Block;
use App\Models\Parking;
use App\Models\Row;
use App\Repositories\Row\RowRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Row\RowResource;

class RowService
{
    /**
     * @var RowRepositoryInterface
     */
    private $repository;

    public function __construct(RowRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = RowResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return RowResource::collection($results)->collection;
    }

    /**
     * @param Row $area
     * @return RowResource
     */
    public function show(Row $area): RowResource
    {
        return new RowResource($area);
    }

    /**
     * @param array $params
     * @return Row
     */
    public function create(array $params): Row
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
     * @param Block $block
     * @return Collection
     */
    public function findAllByBlock(Block $block): Collection
    {
        $results = $this->repository->findAllByBlock($block);

        return RowResource::collection($results)->collection;
    }

    /**
     * @param Row $row
     * @return void
     */
    public function unlinkBlock(Row $row): void
    {
        $this->repository->unlinkBlock($row);
    }

    /**
     * @param Block $block
     * @param array $rows
     * @return void
     */
    public function updateBlockToRows(Block $block, array $rows): void
    {
        $this->repository->updateBlockToRows($block, $rows);
    }

    /**
     * @param Parking $parking
     * @return Collection
     */
    public function findAllByParking(Parking $parking): Collection
    {
        $results = $this->repository->findAllByParking($parking);

        return RowResource::collection($results)->collection;
    }
}
