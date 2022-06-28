<?php

namespace App\Repositories\Vehicle;

use App\Helpers\QueryHelper;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\Slot;
use App\Helpers\QueryParamsHelper;
use App\Models\Row;
use App\Models\Stage;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\BaseRepository;
use App\Repositories\Vehicle\Builders\VehicleDatatablesQueryBuilder;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $vehicle)
    {
        parent::__construct($vehicle);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection
     */
    public function all(Request $request)
    {
        $query = $this->model->query()->with(QueryParamsHelper::getIncludesParamFromRequest());

        if ($vin = $request->query->get('vin')) {
            $query = $query->where('vin', "LIKE", "%{$vin}%");
        }

        $query = $query->orderBy(
            $request->query->get('sort_by', 'id'),
            $request->query->get('sort_direction', 'asc')
        );

        return QueryParamsHelper::checkPaginateParam()
                    ? $query->paginate($request->query->getInt('per_page', 10))
                    : $query->get();
    }

    /**
     * @param Request $request
     * @return Collection
     * @throws Exception
     */
    public function datatables(Request $request): Collection
    {
        $query = (new VehicleDatatablesQueryBuilder($this->model, $request))->getQuery();

        return collect(DataTables::query($query)->make()->getData());
    }

    public function createManual(array $params): Vehicle
    {
        return $this->model->create($params);
    }

    /**
     * Actualizar vehículo.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     * @throws Exception
     */
    public function update(array $params, int $id): ?int
    {
        $vehicle = $this->model->find($id);
        $vehicle->update($params);

        return $vehicle->id;
    }

    /**
     * Borrar Vehículo.
     *
     * @param int $id
     * @return bool|null
     * @throws Exception
     */
    public function delete(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Borrado del Vehículo y relación many to many con stages
            $vehicle = $this->model->find($id);
            $stages = $vehicle->stages()->get();
            foreach ($stages as $stage) {
                $vehicle->stages()->updateExistingPivot($stage->id, ['deleted_at' => Carbon::now()]);
            }
            $vehicle->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * Restaurar Vehículo.
     *
     * @param int $id
     * @return bool|null
     * @throws Exception
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Restauración del Vehículo y relación many to many con stages
            $vehicle = $this->model->withTrashed()->findOrFail($id);
            $stages = $vehicle->stages()->get();
            foreach ($stages as $stage) {
                $vehicle->stages()->updateExistingPivot($stage->id, ['deleted_at' => null]);
            }
            $vehicle->restore();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * @param Row $row
     * @return Collection
     */
    public function findAllByRow(Row $row): Collection
    {
        $slotModel = QueryHelper::escapeNamespaceClass(Slot::class);

        $query = $this->model->query()
            ->select([
                "vehicles.*",
            ])
            ->with(['lastConfirmedMovement', 'lastConfirmedMovement.destinationPosition'])
            ->join(DB::raw("
                (
                    SELECT
                        MAX(id) AS id,
                        vehicle_id
                    FROM
                        movements
                    WHERE
			            destination_position_type = '{$slotModel}' and canceled = 0

                    GROUP BY vehicle_id
                    ORDER BY 1 desc
                ) as last_movement
            "), "vehicles.id", "=", "last_movement.vehicle_id")
            ->join("movements", "last_movement.id", "=", "movements.id")
            ->join("slots", "movements.destination_position_id", "=", "slots.id")
            ->where([
                ["movements.confirmed", "=", 1],
                ["slots.row_id", "=",  $row->id],
                ["slots.fill", "=",  1],
            ])
            ->orderBy("slots.slot_number", "ASC");

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

    /**
     * Obtener la lista de vehículos dado un estado.
     *
     * @param State $state
     * @return Collection
     */
    public function findAllByState(State $state): Collection
    {
        $query = $this->model->query()
            ->whereHas('states', function (Builder $q) use ($state) {
                $q->where('state_id', '=', $state->id);
            });

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

    public function findOneByVin(string $vin): ?Vehicle
    {
        return $this->model->query()->where("vin", $vin)->first();
    }

//    /**
//     * @param Vehicle $vehicle
//     * @param Stage $stage
//     * @param array $params
//     * @return void
//     */
//    private function updateStageAndStateVehicle(Vehicle $vehicle, Stage $stage, array $params): void
//    {
//        /**
//         * Vehículo recibe station "03" y no tiene ningún state le asignamos el state ANNOUNCED.
//         */
//        if ($vehicle->states->count() === 0) {
//            $vehicle->states()->sync([
//                State::STATE_ANNOUNCED_ID => [
//                    "created_at" => Carbon::now(),
//                    "updated_at" => Carbon::now()
//                ]
//            ], false);
//        }
//
//        $hasOnTerminalState = !is_null($vehicle->states->where('id', State::STATE_ON_TERMINAL_ID)->first());
//        $hasCurrentState = !is_null($vehicle->stages->where('id', $stage->id)->first());
//
//        if (
//            !$hasOnTerminalState &&
//            in_array($stage->code, [Stage::STAGE_ST4_CODE, Stage::STAGE_ST5_CODE, Stage::STAGE_ST6_CODE])
//        ) {
//            $vehicle->states()->sync([
//                State::STATE_ON_TERMINAL_ID => [
//                    "created_at" => Carbon::now(),
//                    "updated_at" => Carbon::now()
//                ]
//            ], false);
//        }
//
//        if (!$hasCurrentState) {
//            $vehicle->stages()->sync([
//                $stage->id => [
//                    'manual' => $params['manual'],
//                    'tracking_date' => $params['tracking-date']
//                ]
//            ], false);
//        }
//    }
}
