<?php

namespace App\Repositories\Carrier;

use App\Helpers\QueryParamsHelper;
use App\Models\Carrier;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * @param int $routeTypeId
     * @return Collection
     */
    public function findAllByRouteTypeId(int $routeTypeId): Collection
    {
        return $this->model->query()
                    ->whereHas('routes', function (Builder $q) use ($routeTypeId) {
                        $q->where('route_type_id', "=", $routeTypeId);
                    })
                    ->get();
    }
}
