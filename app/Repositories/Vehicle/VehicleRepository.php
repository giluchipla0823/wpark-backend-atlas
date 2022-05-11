<?php

namespace App\Repositories\Vehicle;

use App\Models\Row;
use App\Models\Slot;
use Exception;
use App\Helpers\QueryParamsHelper;
use App\Models\Vehicle;
use App\Models\Stage;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $vehicle)
    {
        parent::__construct($vehicle);
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

            activity()
                ->withProperties([
                    'lvin' => $params['lvin'],
                    'pvin' => $params['pvin'],
                    'station' => $params['station'],
                    'eoc' => $params['eoc'],
                    'design_id' => $params['design_id'],
                    'color_id' => $params['color_id'],
                    'destination_code_id' => $params['destination_code_id'],
                    'entry_transport_id' => $params['entry_transport_id'],
                    'relations' => [
                        'vehicle_id' => $vehicle->id,
                        'stage_id' => $stage->id,
                        'manual' => $params['manual'],
                        'tracking_date' => $params['tracking-date']
                    ]
                ])
                ->event('Vehículo creado')
                ->log('Tracking-point');

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            activity()
                ->withProperties($params)
                ->event('Error al crear vehículo')
                ->log('Tracking-point');
            throw $e;
        }

        return $vehicle;
    }

    /**
     * Actualizar vehículo.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
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

            activity()
                ->withProperties([
                    'lvin' => $params['lvin'],
                    'pvin' => $params['pvin'],
                    'station' => $params['station'],
                    'eoc' => $params['eoc'],
                    'design_id' => $params['design_id'],
                    'color_id' => $params['color_id'],
                    'destination_code_id' => $params['destination_code_id'],
                    'entry_transport_id' => $params['entry_transport_id'],
                    'relations' => [
                        'vehicle_id' => $vehicle->id,
                        'stage_id' => $stage->id,
                        'manual' => $params['manual'],
                        'tracking_date' => $params['tracking-date']
                    ]
                ])
                ->event('Vehículo actualizado')
                ->log('Tracking-point');

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            activity()
                ->withProperties($params)
                ->event('Error al actualizar vehículo')
                ->log('Tracking-point');
            throw $e;
        }

        return $vehicle->id;
    }

    /**
     * Borrar Vehículo.
     *
     * @param int $id
     * @return bool
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
     * @return bool
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
//        $query = $this->model->query()
//            ->with(['slot'])
//            ->whereHas('slot', function (Builder $q) use ($row) {
//                $q->where('row_id', '=', $row->id)
//                    ->where('fill', '=', 1);
//            });

        $query = $this->model->query()
//            ->with(['lastMovement.destination_slot'])
//            ->whereHas('lastMovement.destination_slot', function (Builder $q) use ($row) {
//                $q->where('row_id', $row->id)
//                  ->where('fill', 1);
//            });
            ->with(['lastMovement', 'lastMovement.destination_slot'])
            ->whereHas('lastMovement.destination_slot', function (Builder $q) use ($row) {
                $q->where('row_id', $row->id)
                    ->where('fill', 1);
            });

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
     * @param string $vin
     * @return Model
     */
    public function findByVin(string $vin): ?Model
    {
        return $this->findBy(['vin' => $vin]);
    }
}
