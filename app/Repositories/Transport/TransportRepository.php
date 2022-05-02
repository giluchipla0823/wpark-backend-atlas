<?php

namespace App\Repositories\Transport;

use App\Helpers\QueryParamsHelper;
use App\Models\Transport;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class TransportRepository extends BaseRepository implements TransportRepositoryInterface
{
    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
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
