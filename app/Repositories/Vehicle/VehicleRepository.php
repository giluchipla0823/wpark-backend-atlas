<?php

namespace App\Repositories\Vehicle;

use App\Helpers\QueryHelper;
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
        $builder = new VehicleDatatablesQueryBuilder($this->model, $request);

        return collect(
            DataTables::query($builder->getQuery())
                ->make(true)
                ->getData()
        );
    }

    public function createManual(array $params): Vehicle
    {
        return $this->model->create($params);
    }

    /**
     * Crear vehículo.
     *
     * @param array $params
     * @return Model
     * @throws Exception
     */
    public function create(array $params): Model
    {
        DB::beginTransaction();

        try {
            // Creación del vehículo y relación many to many con stages
            $vehicle = $this->model->create($params);
            $stage = Stage::where('code', $params['station'])->first();
            $vehicle->stages()->sync([
                $stage->id => [
                    'manual' => $params['manual'],
                    'tracking_date' => $params['tracking-date']
                ]
            ], false);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception('Error al crear vehículo', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $vehicle;
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
        if(!isset($params['station'])){
            $vehicle = $this->model->find($id);
            $vehicle->update($params);
        }else{

            DB::beginTransaction();

            try {
                // Actualización del vehículo y relación many to many con stages
                $vehicle = $this->model->find($id);
                $vehicle->update($params);
                $stage = Stage::where('code', $params['station'])->first();
                $vehicle->stages()->sync([
                    $stage->id => [
                        'manual' => $params['manual'],
                        'tracking_date' => $params['tracking-date']
                    ]
                ], false);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new Exception('Error al actualizar vehículo', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

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
                // "vehicles.id AS vehicle_id"
            ])
            ->with(['lastMovement', 'lastMovement.destinationPosition'])
            ->join(DB::raw("
                    (
                        SELECT
                            m.id AS movement_id,
                            m.vehicle_id AS movement_vehicle_id,
                            m.destination_position_id,
                            m.destination_position_type
                        FROM
                            movements AS m
                        WHERE
                            m.destination_position_type = '". $slotModel ."'
                    ) AS movements
            "), "vehicles.id", "=", DB::raw("
                movements.movement_vehicle_id AND movements.movement_id = (
                    SELECT
                            lmov.id
                        FROM
                            movements AS lmov
                        WHERE
                            lmov.vehicle_id = vehicles.id AND lmov.confirmed = 1
                        ORDER BY
                            lmov.id DESC
                        LIMIT 1
                )
            "))
            ->join("slots", "movements.destination_position_id", "=", "slots.id")
            ->where([
                ["slots.row_id", "=",  $row->id],
                ["slots.fill", "=",  1],
            ])
            // ->exclude(['slots.id'])
            ->orderBy("movements.destination_position_id", "ASC");

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
}
