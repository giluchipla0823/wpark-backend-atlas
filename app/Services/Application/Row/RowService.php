<?php

namespace App\Services\Application\Row;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Row\RowResource;
use App\Http\Resources\Row\RowShowResource;
use App\Models\Block;
use App\Models\Parking;
use App\Models\Row;
use App\Repositories\Row\RowRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

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
     * @return RowShowResource
     */
    public function show(Row $area): RowShowResource
    {
        return new RowShowResource($area);
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
     * @param Block $block
     * @return void
     */
    public function updateBlock(Row $row, Block $block): void
    {
        $this->repository->updateBlock($row, $block);
    }

    /**
     * @param Row $row
     * @return void
     */
    public function unlinkBlock(Row $row): void
    {
        $this->repository->updateBlock($row);
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

    /**
     * @param Row $row
     * @param string|null $comments
     * @return int
     */
    public function toggleActive(Row $row, ?string $comments = null): int {
        $active = $row->active ? 0 : 1;

        $this->update([
            "active" => $active,
            "comments" => $comments,
        ], $row->id);

        return $active;
    }

    /**
     * @param string $value
     * @return RowShowResource
     * @throws Exception
     */
    public function findOneByQrcode(string $value): RowShowResource
    {
        if (!$row = $this->repository->findOneByQrcode($value)) {
            throw new Exception(
                "No se encontró información de la fila con el QR {$value}",
                Response::HTTP_NOT_FOUND
            );
        }

        return new RowShowResource($row);
    }
}
