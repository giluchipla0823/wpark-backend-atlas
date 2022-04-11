<?php

namespace App\Repositories\Block;

use App\Helpers\QueryParamsHelper;
use App\Models\Block;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class BlockRepository extends BaseRepository implements BlockRepositoryInterface
{
    public function __construct(Block $block)
    {
        parent::__construct($block);
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

        if ($name = $request->query->get('name')) {
            $query = $query->where('name', 'LIKE', "%" . $name . "%");
        }

        $sortBy = $request->query->get('sort_by', 'id');
        $sortDirection = $request->query->get('sort_direction', 'asc');
        $query = $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }
}
