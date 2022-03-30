<?php

namespace App\Repositories\Brand;

use App\Helpers\QueryParamsHelper;
use App\Models\Brand;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $brand)
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
