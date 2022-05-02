<?php

namespace App\Repositories\State;

use App\Helpers\QueryParamsHelper;
use App\Models\State;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    public function __construct(State $state)
    {
        parent::__construct($state);
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

        if ($modelStateId = $request->query->get('model_state_id')) {
            $query = $query->where('model_state_id', '=', $modelStateId);
        }

        return $query->get();
    }

}
