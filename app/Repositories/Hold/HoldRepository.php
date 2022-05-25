<?php

namespace App\Repositories\Hold;

use App\Helpers\QueryParamsHelper;
use App\Models\Hold;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HoldRepository extends BaseRepository implements HoldRepositoryInterface
{
    public function __construct(Hold $hold)
    {
        parent::__construct($hold);
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
     * Crear Hold.
     *
     * @param array $params
     * @return Model
     */
    public function create(array $params): Model
    {
        DB::beginTransaction();

        try {
            // Creación del Hold y relación many to many con condiciones
            $hold = $this->model->create($params);
            $hold->conditions()->sync($params['conditions']);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }

        return $hold;
    }

    /**
     * Actualizar Hold.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        DB::beginTransaction();

        try {
            // Actualización del Hold y relación many to many con condiciones
            $hold = $this->model->find($id);
            $hold->update($params);

            if (array_key_exists('conditions', $params) && is_array($params['conditions'])) {
                $hold->conditions()->sync($params['conditions']);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }

        return $hold->id;
    }

    /**
     * Borrar Hold.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Borrado del Hold y relación many to many con condiciones
            $hold = $this->model->find($id);
            $conditions = $hold->conditions()->get();
            foreach($conditions as $condition){
                $hold->conditions()->updateExistingPivot($condition->id, ['deleted_at' => Carbon::now()]);
            }
            $hold->delete();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * Restaurar Hold.
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Restauración del Hold y relación many to many con condiciones
            $hold = $this->model->withTrashed()->findOrFail($id);
            $conditions = $hold->conditions()->get();
            foreach($conditions as $condition){
                $hold->conditions()->updateExistingPivot($condition->id, ['deleted_at' => null]);
            }
            $hold->restore();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }
        return true;
    }

}
