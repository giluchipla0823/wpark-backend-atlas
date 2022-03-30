<?php

namespace App\Repositories\Design;

use App\Helpers\QueryParamsHelper;
use App\Models\Design;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class DesignRepository extends BaseRepository implements DesignRepositoryInterface
{
    public function __construct(Design $design)
    {
        parent::__construct($design);
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
