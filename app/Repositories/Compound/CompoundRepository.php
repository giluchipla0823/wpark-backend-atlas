<?php

namespace App\Repositories\Compound;

use App\Helpers\QueryParamsHelper;
use App\Models\Compound;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class CompoundRepository extends BaseRepository implements CompoundRepositoryInterface
{
    public function __construct(Compound $compound)
    {
        parent::__construct($compound);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        return $this->model->all();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        return Datatables::customizable($query)->response();
    }
}
