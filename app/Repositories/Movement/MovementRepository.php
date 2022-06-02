<?php

namespace App\Repositories\Movement;

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
        $query = $this->model->query();

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $builder = new MovementDatatablesQueryBuilder($this->model, $request);

        return Datatables::customizable($builder->getQuery())->response();
    }
}
