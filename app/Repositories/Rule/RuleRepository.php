<?php

namespace App\Repositories\Rule;

use App\Helpers\QueryParamsHelper;
use App\Models\Rule;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RuleRepository extends BaseRepository implements RuleRepositoryInterface
{
    public function __construct(Rule $rule)
    {
        parent::__construct($rule);
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

    // TODO: Asociar correctamente las relaciones para crear, actualizar, borrar y restaurar

    /**
     * Crear Regla.
     *
     * @param array $params
     * @return Model
     */
    public function create(array $params): Model
    {
        DB::beginTransaction();

        try {
            // Creación de la regla y relación many to many con condiciones y bloques
            $rule = $this->model->create($params);

            $rule->blocks()->sync($params['blocks']);

            $conditions = $params['conditions'];

            foreach ($conditions as $condition) {
                $rule->conditions()->attach($condition['condition_id'], [
                    'conditionable_type' => $condition['conditionable_type'],
                    'conditionable_id' => $condition['conditionable_id']
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $rule;
    }

    /**
     * Actualizar Regla.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        DB::beginTransaction();

        try {
            // Actualización de la regla y relación many to many con condiciones y bloques
            $rule = $this->model->find($id);
            $rule->update($params);

            if (array_key_exists('blocks', $params) && is_array($params['blocks'])) {
                $rule->blocks()->sync($params['blocks']);
            }

            if (array_key_exists('conditions', $params) && is_array($params['conditions'])) {
                $conditions = $params['conditions'];
                $rule->conditions()->detach();
                foreach ($conditions as $condition) {
                    $rule->conditions()->attach([$condition['condition_id'] => [
                        'conditionable_type' => $condition['conditionable_type'],
                        'conditionable_id' => $condition['conditionable_id']
                    ]]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $rule->id;
    }

    /**
     * Borrar Regla.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Borrado de la regla y relación many to many con condiciones y bloques
            $rule = $this->model->find($id);

            $blocks = $rule->blocks()->get();
            foreach ($blocks as $block) {
                $rule->blocks()->updateExistingPivot($block->id, ['deleted_at' => Carbon::now()]);
            }

            $conditions = $rule->conditions()->get();
            foreach ($conditions as $condition) {
                $rule->conditions()->updateExistingPivot($condition->id, ['deleted_at' => Carbon::now()]);
            }
            $rule->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * Restaurar Regla.
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Restauración de la regla y relación many to many con condiciones y bloques
            $rule = $this->model->withTrashed()->findOrFail($id);

            $blocks = $rule->blocks()->get();
            foreach ($blocks as $block) {
                $rule->blocks()->updateExistingPivot($block->id, ['deleted_at' => null]);
            }

            $conditions = $rule->conditions()->get();
            foreach ($conditions as $condition) {
                $rule->conditions()->updateExistingPivot($condition->id, ['deleted_at' => null]);
            }
            $rule->restore();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return true;
    }
}
