<?php

namespace App\Services\Block;

use App\Models\Block;
use App\Repositories\Block\BlockRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Block\BlockResource;

class BlockService
{
    /**
     * @var BlockRepositoryInterface
     */
    private $repository;

    public function __construct(BlockRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = BlockResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return BlockResource::collection($results)->collection;
    }

    /**
     * @param Block $block
     * @return BlockResource
     */
    public function show(Block $block): BlockResource
    {
        return new BlockResource($block);
    }

    /**
     * @param array $params
     * @return Block
     */
    public function create(array $params): Block
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
