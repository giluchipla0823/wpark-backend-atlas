<?php

namespace App\Repositories\Row;

use App\Helpers\QueryParamsHelper;
use App\Models\Block;
use App\Models\Parking;
use App\Models\Row;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class RowRepository extends BaseRepository implements RowRepositoryInterface
{
    public function __construct(Row $area)
    {
        parent::__construct($area);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

    /**
     * Obtener la lista de rows dado un block.
     *
     * @param Block $block
     * @return Collection
     */
    public function findAllByBlock(Block $block): Collection
    {
        return $this->model->query()
                    ->where('block_id', $block->id)
                    ->get();
    }

    /**
     * Desvincular el bloque actual de una fila.
     *
     * @param Row $row
     * @return void
     */
    public function unlinkBlock(Row $row): void {
        $this->model->query()
             ->where('id', $row->id)
             ->update(['block_id' => null]);
    }

    /**
     * @param Block $block
     * @param array $rows
     * @return void
     */
    public function updateBlockToRows(Block $block, array $rows): void
    {
        $this->model->query()
            ->whereIn('id', $rows)
            ->update(['block_id' => $block->id]);
    }

    /**
     * Obtener la lista de rows dado un parking.
     *
     * @param Parking $parking
     * @return Collection
     */
    public function findAllByParking(Parking $parking): Collection
    {
        return $this->model->query()
            ->where('parking_id', $parking->id)
            ->get();
    }
}
