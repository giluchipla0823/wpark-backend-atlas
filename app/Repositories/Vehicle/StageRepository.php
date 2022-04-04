<?php

namespace App\Repositories\Vehicle;

use App\Helpers\QueryParamsHelper;
use App\Models\Stage;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class StageRepository extends BaseRepository implements StageRepositoryInterface
{
    public function __construct(Stage $stage)
    {
        parent::__construct($stage);
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
