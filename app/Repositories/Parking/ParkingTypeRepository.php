<?php

namespace App\Repositories\Parking;

use App\Helpers\QueryParamsHelper;
use App\Models\ParkingType;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class ParkingTypeRepository extends BaseRepository implements ParkingTypeRepositoryInterface
{
    public function __construct(ParkingType $parkingType)
    {
        parent::__construct($parkingType);
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

}
