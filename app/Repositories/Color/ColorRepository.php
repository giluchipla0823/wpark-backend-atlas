<?php

namespace App\Repositories\Color;

use App\Helpers\QueryParamsHelper;
use App\Models\Color;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class ColorRepository extends BaseRepository implements ColorRepositoryInterface
{
    public function __construct(Color $color)
    {
        parent::__construct($color);
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
