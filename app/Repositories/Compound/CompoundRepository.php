<?php

namespace App\Repositories\Compound;

use App\Helpers\QueryParamsHelper;
use App\Models\Compound;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class CompoundRepository extends BaseRepository implements CompoundRepositoryInterface
{
    public function __construct(Compound $compound)
    {
        parent::__construct($compound);
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
