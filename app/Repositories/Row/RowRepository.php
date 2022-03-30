<?php

namespace App\Repositories\Row;

use App\Helpers\QueryParamsHelper;
use App\Models\Row;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
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

}
