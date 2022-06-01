<?php

namespace App\Repositories\Rule;

use App\Helpers\QueryParamsHelper;
use App\Models\Block;
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

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if ($request->query->has("is_group")) {
            $isGroup = $request->query->getInt("is_group");

            $query = $query->where("is_group", "=", $isGroup);
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

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if ($request->query->has("is_group")) {
            $isGroup = $request->query->getInt("is_group");

            $query = $query->where("is_group", "=", $isGroup);
        }

        return Datatables::customizable($query)->response();
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

            $presortingBlock = Block::where('presorting_default', 1)->first();

            if (!$params['is_group']) {

                $rule->blocks()->sync([$presortingBlock->id, $params['block_id']]);

                $conditions = $params['conditions'];

                foreach ($conditions as $condition) {
                    $rule->conditions()->attach($condition['condition_id'], [
                        'conditionable_type' => $condition['conditionable_type'],
                        'conditionable_id' => $condition['conditionable_id']
                    ]);
                }
            } else {
                $rule->rules_groups()->sync($params['rules']);
                $rule->blocks()->sync($presortingBlock->id);
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

            $presortingBlock = Block::where('presorting_default', 1)->first();

            if (!$params['is_group']) {

                $rule->blocks()->sync([$presortingBlock->id, $params['block_id']]);

                $conditions = $params['conditions'];

                foreach ($conditions as $condition) {
                    $rule->conditions()->attach($condition['condition_id'], [
                        'conditionable_type' => $condition['conditionable_type'],
                        'conditionable_id' => $condition['conditionable_id']
                    ]);
                }
            } else {
                $rule->rules_groups()->sync($params['rules']);
                $rule->blocks()->sync($presortingBlock->id);
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
