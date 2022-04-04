<?php

namespace App\Repositories\DestinationCode;

use App\Helpers\QueryParamsHelper;
use App\Models\DestinationCode;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class DestinationCodeRepository extends BaseRepository implements DestinationCodeRepositoryInterface
{
    public function __construct(DestinationCode $destinationCode)
    {
        parent::__construct($destinationCode);
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
