<?php

namespace App\Repositories\Design;

use App\Helpers\QueryParamsHelper;
use App\Models\Design;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class DesignRepository extends BaseRepository implements DesignRepositoryInterface
{
    public function __construct(Design $design)
    {
        parent::__construct($design);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        return Datatables::customizable($query)->response();
    }

    /**
     * @param string $code
     * @return Model|null
     */
    public function findByCode(string $code): ?Model
    {
        return $this->findBy(['code' => $code]);
    }

}
