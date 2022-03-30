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

        return $query->get();
    }

}
