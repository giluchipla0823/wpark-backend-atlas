<?php

namespace App\Services\Block;

use App\Models\Block;
use App\Repositories\Block\BlockRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Block\BlockResource;
use Illuminate\Support\Facades\DB;

class BlockService
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var RowRepositoryInterface
     */
    private $rowRepository;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        RowRepositoryInterface $rowRepository
    )
    {
        $this->blockRepository = $blockRepository;
        $this->rowRepository = $rowRepository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->blockRepository->all($request);

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
        return $this->blockRepository->create($params);
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->blockRepository->update($params, $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->blockRepository->delete($id);
    }

    public function restore(int $id): void
    {
        $this->blockRepository->restore($id);
    }

    /**
     * @param Block $block
     * @return int
     */
    public function toggleActive(Block $block): int {
        $active = $block->active ? 0 : 1;

        $this->update(['active' => $active], $block->id);

        return $active;
    }

    /**
     * @param Block $block
     * @param array $rows
     * @return void
     */
    public function addRows(Block $block, array $rows): void
    {
        $this->blockRepository->addRows($block, $rows);
    }
}
