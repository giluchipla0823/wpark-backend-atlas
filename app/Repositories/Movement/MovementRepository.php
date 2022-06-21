<?php

namespace App\Repositories\Movement;

use Exception;
use App\Models\Movement;
use App\Repositories\BaseRepository;
use App\Repositories\Movement\Builders\MovementDatatablesQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class MovementRepository extends BaseRepository implements MovementRepositoryInterface
{
    public function __construct(Movement $movement)
    {
        parent::__construct($movement);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        return $this->model->query()->get();
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function datatables(Request $request): array
    {
        $query = (new MovementDatatablesQueryBuilder($this->model, $request))->getQuery();

        return collect(DataTables::eloquent($query)->make()->getData())->all();
    }
}
