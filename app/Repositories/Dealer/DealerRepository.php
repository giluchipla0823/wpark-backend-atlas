<?php

namespace App\Repositories\Dealer;

use App\Helpers\QueryParamsHelper;
use App\Models\Dealer;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class DealerRepository extends BaseRepository implements DealerRepositoryInterface
{
    public function __construct(Dealer $dealer)
    {
        parent::__construct($dealer);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $table = $this->model->getTable();
        $query = $this->model->query()->with(QueryParamsHelper::getIncludesParamFromRequest());

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $query = $query->select(["{$table}.*"]);
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

}
