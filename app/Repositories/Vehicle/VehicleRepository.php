<?php

namespace App\Repositories\Vehicle;

use App\Models\Parking;
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

        return $query->get();
    }

    /**
     * @param Request $request
     * @return mixed
     */

    /**
     * @param Request $request
     * @return Collection
     * @throws Exception
     */
    public function datatables(Request $request): Collection
    {
        $slotModel = str_replace("\\", "\\\\\\", Slot::class);
        $parkingModel = str_replace("\\", "\\\\\\", Parking::class);

        $positionSubQuery = DB::raw("
            (
                SELECT
                    lmov2.id AS movement_id,
                    lmov2.origin_position_type,
                    lmov2.destination_position_type,
                    IF(lmov2.origin_position_type = '$slotModel', slot_origin.slot_id, parking_origin.id) AS origin_position_id,
                    IF(lmov2.origin_position_type = '$slotModel', CONCAT(slot_origin.parking_name, '.', LPAD(slot_origin.`row_number`, 3, '0')), parking_origin.name) AS origin_position_name,
                    IF(lmov2.origin_position_type = '$slotModel', slot_origin.slot_number, null) AS origin_position_slot,
                    IF(lmov2.destination_position_type = '$slotModel', slot_destination.slot_id, parking_destination.id) AS destination_position_id,
                    IF(lmov2.destination_position_type = '$slotModel', CONCAT(slot_destination.parking_name, '.', LPAD(slot_destination.`row_number`, 3, '0')), parking_destination.name) AS destination_position_name,
                    IF(lmov2.destination_position_type = '$slotModel', slot_destination.slot_number, null) AS destination_position_slot
                FROM
                    movements AS lmov2
                LEFT JOIN
                 (
                    SELECT
                        park.id AS parking_id,
                        park.name AS parking_name,
                        r.id AS row_id,
                        r.`row_number` AS `row_number`,
                        slot.id AS slot_id,
                        slot.slot_number AS slot_number
                    FROM
                        slots AS slot
                        INNER JOIN `rows` AS r on slot.row_id = r.id
                        INNER JOIN parkings AS park on r.parking_id = park.id
                ) AS slot_origin ON lmov2.origin_position_id = slot_origin.slot_id AND lmov2.origin_position_type = '$slotModel'
                LEFT JOIN
                 (
                    SELECT
                        park.id AS parking_id,
                        park.name AS parking_name,
                        r.id AS row_id,
                        r.`row_number` AS `row_number`,
                        slot.id AS slot_id,
                        slot.slot_number AS slot_number
                    FROM
                        slots AS slot
                        INNER JOIN `rows` AS r on slot.row_id = r.id
                        INNER JOIN parkings AS park on r.parking_id = park.id
                ) AS slot_destination ON lmov2.destination_position_id = slot_destination.slot_id AND lmov2.destination_position_type = '$slotModel'
                LEFT JOIN
                    parkings AS parking_origin ON lmov2.origin_position_id = parking_origin.id AND lmov2.origin_position_type = '$parkingModel'
                LEFT JOIN
                    parkings AS parking_destination ON lmov2.destination_position_id = parking_destination.id AND lmov2.destination_position_type = '$parkingModel'
                WHERE
                    lmov2.confirmed = 1
            ) AS position
        ");

        $query = DB::table($this->model->getTable())
                    ->select([
                        "vehicles.id",
                        "vehicles.vin",
                        "vehicles.vin_short",
                        "colors.id AS color_id",
                        "colors.name AS color_name",
                        "designs.id AS design_id",
                        "designs.name AS design_name",
                        "brands.id AS brand_id",
                        "brands.name AS brand_name",
                        "destination_codes.id AS destination_code_id",
                        "destination_codes.name AS destination_code_name",
                        "destination_codes.code AS destination_code_codification",
                        "countries.id AS country_id",
                        "countries.name AS country_name",
                        "countries.code AS country_code",
                        "shipping_rules.id AS shipping_rule_id",
                        "shipping_rules.name AS shipping_rule_name",
                        "carriers_shipping_rules.id AS carrier_id",
                        "carriers_shipping_rules.name AS carrier_name",
                        "carriers_shipping_rules.short_name AS carrier_short_name",
                        "carriers_shipping_rules.code AS carrier_code",
                        "states.id AS state_id",
                        "states.name AS state_name",
                        "states.description AS state_description",
                        "last_state_vehicle.created_at AS state_created_at",
                        "onterminal_state_vehicle.dt_terminal",
                        "transports_entry.id AS transport_entry_id",
                        "transports_entry.name AS transport_entry_name",
                        "transports_exit.transport_id AS transport_exit_id",
                        "transports_exit.transport_name AS transport_exit_name",
                        "stages.id AS stage_id",
	                    "stages.name AS stage_name",
	                    "stages.description AS stage_description",
                        "position.origin_position_type",
                        'position.origin_position_id',
                        "position.origin_position_name",
                        "position.origin_position_slot",
                        "position.destination_position_type",
                        "position.destination_position_id",
                        "position.destination_position_name",
                        "position.destination_position_slot"
                    ])
                    ->join('colors', 'vehicles.color_id', '=', 'colors.id')
                    ->join('designs', 'vehicles.design_id', '=', 'designs.id')
                    ->join('brands', 'designs.brand_id', '=', 'brands.id')
                    ->join('destination_codes', 'vehicles.destination_code_id', '=', 'destination_codes.id')
                    ->join('countries', 'destination_codes.country_id', '=', 'countries.id')
                    ->leftJoin('transports AS transports_entry', "vehicles.entry_transport_id", "=", "transports_entry.id")
                    ->leftJoin(DB::raw("
                        (
                            SELECT
                                lo.id AS load_id,
                                t.id AS transport_id,
                                t.name AS transport_name
                            FROM
                                loads AS lo
                            INNER JOIN
                                transports AS t ON lo.exit_transport_id = lo.id
                        ) AS transports_exit
                    "), "vehicles.load_id", "=", "transports_exit.load_id")
                    ->leftJoin('rules AS shipping_rules', 'vehicles.shipping_rule_id', '=', 'shipping_rules.id')
                    ->leftJoin('carriers AS carriers_shipping_rules', 'shipping_rules.carrier_id', '=', 'carriers_shipping_rules.id')
                    ->leftJoin(DB::raw("
                        (
                            SELECT
                                otvs.id,
                                otvs.vehicle_id,
                                otvs.created_at AS dt_terminal
                            FROM
                                vehicles_states AS otvs
                            WHERE
                                otvs.state_id = 2
                        ) AS onterminal_state_vehicle
                    "), 'vehicles.id', '=', 'onterminal_state_vehicle.vehicle_id')
                    ->leftJoin('vehicles_states AS last_state_vehicle', 'vehicles.id', '=', DB::raw('
                        last_state_vehicle.vehicle_id AND last_state_vehicle.id = (
                            SELECT
                                lvs.id
                            FROM
                                vehicles_states AS lvs
                            WHERE
                                lvs.vehicle_id = vehicles.id
                            ORDER BY
                                lvs.created_at DESC
                            LIMIT 1
                        )
                    '))
                    ->leftJoin('states', 'last_state_vehicle.state_id', '=', 'states.id')
                    ->leftJoin('vehicles_stages AS last_stage_vehicle', 'vehicles.id', '=', DB::raw('
                        last_stage_vehicle.vehicle_id AND last_stage_vehicle.id = (
                            SELECT
                                lvst.id
                            FROM
                                vehicles_stages AS lvst
                            WHERE
                                lvst.vehicle_id = vehicles.id
                            ORDER BY
                                lvst.created_at DESC
                            LIMIT 1
                        )
                    '))
                    ->leftJoin('stages', 'last_stage_vehicle.stage_id', '=', 'stages.id')
                    ->leftJoin('movements AS last_movement', 'vehicles.id', '=',  DB::raw('
                        last_movement.vehicle_id AND last_movement.id = (
                            SELECT
                                lmov.id
                            FROM
                                movements AS lmov
                            WHERE
                                lmov.vehicle_id = vehicles.id AND lmov.confirmed = 1
                            ORDER BY
                                lmov.created_at DESC
                            LIMIT 1
                        )
                    '))
            ->leftJoin($positionSubQuery, 'last_movement.id', '=', 'position.movement_id');

        // Filters
        if ($states = $request->get('states')) {
            $states = explode(',', $states);

            $query = $query->whereIn('states.id', $states);
        }

        return collect(DataTables::query($query)->make(true)->getData());
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
        $query = $this->model->query()
            ->with(['lastMovement', 'lastMovement.destination_slot'])
            ->whereHas('lastMovement', function (Builder $q) use ($row) {
                $q->where('confirmed', 1)
                  ->whereHas('destination_slot', function (Builder $q) use ($row) {
                        $q->where([
                            ['row_id', '=', $row->id],
                            ['fill', '=', 1],
                        ]);
                  });
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
