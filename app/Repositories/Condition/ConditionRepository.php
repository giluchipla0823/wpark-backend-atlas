<?php

namespace App\Repositories\Condition;

use App\Helpers\QueryParamsHelper;
use App\Models\Condition;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class ConditionRepository extends BaseRepository implements ConditionRepositoryInterface
{
    public function __construct(Condition $brand)
    {
        parent::__construct($brand);
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
