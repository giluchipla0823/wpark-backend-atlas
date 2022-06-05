<?php

namespace App\Repositories\Row;

use App\Helpers\QueryParamsHelper;
use App\Models\Block;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
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
     * @param Row $row
     * @param Block|null $block
     * @return void
     */
    public function updateBlock(Row $row, ?Block $block = null): void
    {
        $this->model->query()
            ->where('id', $row->id)
            ->update(['block_id' => $block ? $block->id : null]);
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
            ->with(QueryParamsHelper::getIncludesParamFromRequest())
            ->where('parking_id', $parking->id)
            ->get();
    }

    /**
     * Obtener la lista de rows dado un parking.
     *
     * @param Parking $parking
     * @return Collection
     */
    public function findAllBySpykesParking(Parking $parking): Collection
    {
        if($parking->parking_type_id !== ParkingType::TYPE_ESPIGA){
            throw new Exception('El parking no es de tipo espiga', Response::HTTP_BAD_REQUEST );
        }

        return $this->model->query()
            ->where('parking_id', $parking->id)
            ->with(['slots.destinationMovement.vehicle' , 'slots.destinationMovement' => function($q){
                $q->where('confirmed', 1);
            }])
            ->get();
    }

    /**
     * @param $value
     * @return Row|null
     */
    public function findOneByQrcode($value): ?Row
    {
        return $this->model->query()
                    ->where("alt_qr", "=", $value)
                    ->first();
    }
}
