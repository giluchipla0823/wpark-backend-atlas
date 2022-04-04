<?php

namespace App\Repositories\Route;

use App\Helpers\QueryParamsHelper;
use App\Models\Route;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class RouteRepository extends BaseRepository implements RouteRepositoryInterface
{
    public function __construct(Route $route)
    {
        parent::__construct($route);
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
