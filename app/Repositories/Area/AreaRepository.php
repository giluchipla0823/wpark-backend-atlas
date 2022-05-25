<?php

namespace App\Repositories\Area;

use App\Helpers\QueryParamsHelper;
use App\Models\Area;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class AreaRepository extends BaseRepository implements AreaRepositoryInterface
{
    public function __construct(Area $area)
    {
        parent::__construct($area);
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
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            // Seleccionar todos los parkings asociados a el área para borrarlos junto con sus filas y slots
            $parkings = $this->model->find($id)->parkings;

            foreach ($parkings as $parking) {
                $rows = $parking->rows;
                foreach ($rows as $row) {
                    $slots = $row->slots;
                    foreach ($slots as $slot) {
                        $slot->delete();
                    }
                    $row->delete();
                }
                $parking->delete();
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
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Seleccionar todos los parkings asociados a el área para restaurarlos junto con sus filas y slots
            $parkings = $this->model->withTrashed()->findOrFail($id)->parkings()->withTrashed()->get();

            foreach ($parkings as $parking) {
                $rows = $parking->rows()->withTrashed()->get();
                foreach ($rows as $row) {
                    $slots = $row->slots()->withTrashed()->get();
                    foreach ($slots as $slot) {
                        $slot->restore();
                    }
                    $row->restore();
                }
                $parking->restore();
            }

            $this->model->withTrashed()->findOrFail($id)->restore();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return true;
    }

    public function hello_world(): string
    {
        return 'hola';
    }
}
