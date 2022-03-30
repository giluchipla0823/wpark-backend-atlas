<?php

namespace App\Repositories\Slot;

use App\Helpers\QueryParamsHelper;
use App\Models\Slot;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class SlotRepository extends BaseRepository implements SlotRepositoryInterface
{
    public function __construct(Slot $area)
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
