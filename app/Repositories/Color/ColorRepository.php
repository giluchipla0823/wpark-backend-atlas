<?php

namespace App\Repositories\Color;

use App\Helpers\QueryParamsHelper;
use App\Models\Color;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class ColorRepository extends BaseRepository implements ColorRepositoryInterface
{
    public function __construct(Color $color)
    {
        parent::__construct($color);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        if ($request->query->getBoolean('can_exclude_unknown')) {
            $query = $query->where("id", "!=", Color::UNKNOWN_ID);
        }

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $table = $this->model->getTable();
        $query = $this->model->query()
            ->select(["{$table}.*"]);

        if ($request->query->getBoolean('can_exclude_unknown')) {
            $query = $query->where("id", "!=", Color::UNKNOWN_ID);
        }

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
