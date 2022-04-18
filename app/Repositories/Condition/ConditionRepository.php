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
    public function __construct(Condition $condition)
    {
        parent::__construct($condition);
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

        if ($modelConditionId = $request->query->get('model_condition_id')) {
            $query = $query->where('model_condition_id', '=', $modelConditionId);
        }

        return $query->get();
    }

}
