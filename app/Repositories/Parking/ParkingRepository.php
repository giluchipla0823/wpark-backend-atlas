<?php

namespace App\Repositories\Parking;

use Exception;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class ParkingRepository extends BaseRepository implements ParkingRepositoryInterface
{
    public function __construct(Parking $parking)
    {
        parent::__construct($parking);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query()->with(QueryParamsHelper::getIncludesParamFromRequest());

        if ($name = $request->query->get('name')) {
            $query = $query->where('name', 'LIKE', "%" . $name . "%");
        }

        $sortBy = $request->query->get('sort_by', 'id');
        $sortDirection = $request->query->get('sort_direction', 'asc');
        $query = $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query()
                      ->with(QueryParamsHelper::getIncludesParamFromRequest());

        return Datatables::customizable($query)->response();
    }

    /**
     * @param array $params
     * @return Model
     */
    public function create(array $params): Model
    {
        return $this->model->create($params);
    }

    /**
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        return $this->model->where('id', $id)->update($params);
    }

    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            // Seleccionar todas las filas asociadas al parking para borrarlas junto con sus slots
            $rows = $this->model->find($id)->rows;

            foreach ($rows as $row) {
                $slots = $row->slots;
                foreach ($slots as $slot) {
                    $slot->delete();
                }
                $row->delete();
            }

            $this->model->destroy($id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Seleccionar todas las filas asociadas al parking para restaurarlas junto con sus slots
            $rows = $this->model->withTrashed()->findOrFail($id)->rows()->withTrashed()->get();

            foreach ($rows as $row) {
                $slots = $row->slots()->withTrashed()->get();
                foreach ($slots as $slot) {
                    $slot->restore();
                }
                $row->restore();
            }

            $this->model->withTrashed()->findOrFail($id)->restore();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return true;
    }
}
