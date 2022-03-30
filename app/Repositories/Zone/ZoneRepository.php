<?php

namespace App\Repositories\Zone;

use App\Helpers\QueryParamsHelper;
use App\Models\Zone;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class ZoneRepository extends BaseRepository implements ZoneRepositoryInterface
{
    public function __construct(Zone $zone)
    {
        parent::__construct($zone);
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
