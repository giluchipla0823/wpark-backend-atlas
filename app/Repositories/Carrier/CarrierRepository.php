<?php

namespace App\Repositories\Carrier;

use App\Helpers\QueryParamsHelper;
use App\Models\Carrier;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class CarrierRepository extends BaseRepository implements CarrierRepositoryInterface
{
    public function __construct(Carrier $carrier)
    {
        parent::__construct($carrier);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        return $this->model->all();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        return DataTables::customizable($query)->response();
    }
}
