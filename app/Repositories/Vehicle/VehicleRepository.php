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
        $query = $this->model->query()
            ->select([
                "vehicles.*",
            ])
            ->with(['lastConfirmedMovement', 'lastConfirmedMovement.destinationPosition'])
            ->join(DB::raw("
                (
                    SELECT
                        MAX(movements.id) AS id,
                        movements.vehicle_id
                    FROM
                        movements
                    WHERE
                        movements.canceled = 0
                    GROUP BY movements.vehicle_id
                ) as last_movement
            "), "vehicles.id", "=", "last_movement.vehicle_id")
            ->join("movements", "last_movement.id", "=", "movements.id")
            ->join("slots", "movements.destination_position_id", "=", "slots.id")
            ->where([
                ["movements.confirmed", "=", 1],
                ["movements.destination_position_type", "=", Slot::class],
                ["slots.row_id", "=",  $row->id],
                ["slots.fill", ">",  0],
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

    /**
     * Obtener vehículo por vin.
     *
     * @param string $vin
     * @return Vehicle|null
     */
    public function findOneByVin(string $vin): ?Vehicle
    {
        return $this->model->query()->where("vin", $vin)->first();
    }
}
