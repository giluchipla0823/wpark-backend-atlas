<?php

namespace App\Repositories\Vehicle\Builders;

use App\Models\Slot;
use App\Models\Stage;
use App\Models\State;
use App\Models\Vehicle;
use App\Models\Parking;
use App\Helpers\DatesHelper;
use App\Helpers\QueryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

class VehicleDatatablesQueryBuilder
{
    /**
     * @var Vehicle
     */
    private $model;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Builder
     */
    private $query;

    private static $statesReferenceColumnToSearch = [
        State::STATE_ANNOUNCED_ID => 'dt_announced',
        State::STATE_ON_TERMINAL_ID => 'dt_terminal',
        State::STATE_LEFT_ID => 'dt_left',
    ];

    public function __construct(Vehicle $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        $this->query = DB::table($this->model->getTable())
            ->select([
                "vehicles.id",
                "vehicles.vin",
                "vehicles.vin_short",
                "vehicles.info",
                DB::raw("(CASE WHEN vehicles.load_id IS NOT NULL THEN 1 ELSE 0 END) as is_load"),
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
                "carriers_route_load.carrier_id",
                "carriers_route_load.carrier_name",
                "carriers_route_load.carrier_short_name",
                "carriers_route_load.carrier_code",
                "carriers_route_load.carrier_belongs_to",
                "transports_entry.id AS transport_entry_id",
                "transports_entry.name AS transport_entry_name",
                "transports_exit.transport_id AS transport_exit_id",
                "transports_exit.transport_name AS transport_exit_name",
                "transports_exit.load_id AS load_id",
                "transports_exit.load_transport_identifier AS load_transport_identifier",
                "last_stage_vehicle.id AS stage_id",
                "last_stage_vehicle.name AS stage_name",
                "last_stage_vehicle.description AS stage_description",
                "last_stage_vehicle.dt_gate_release",
                "last_state_vehicle.id AS state_id",
                "last_state_vehicle.name AS state_name",
                "last_state_vehicle.description AS state_description",
                "last_state_vehicle.created_at AS state_created_at",
                "last_state_vehicle.dt_announced",
                "last_state_vehicle.dt_terminal",
                "last_state_vehicle.dt_left",
                "position.*",
            ])
            ->join('colors', 'vehicles.color_id', '=', 'colors.id')
            ->join('designs', 'vehicles.design_id', '=', 'designs.id')
            ->join('brands', 'designs.brand_id', '=', 'brands.id')
            ->join('destination_codes', 'vehicles.destination_code_id', '=', 'destination_codes.id')
            ->join('countries', 'destination_codes.country_id', '=', 'countries.id')
            ->join('transports AS transports_entry', "vehicles.entry_transport_id", "=", "transports_entry.id")
            ->leftJoin(DB::raw("({$this->getLastStateVehicleSubQuery()}) AS last_state_vehicle"), 'vehicles.id', "=", "last_state_vehicle.vehicle_id")
            ->leftJoin(DB::raw("({$this->getLastStageVehicleSubQuery()}) AS last_stage_vehicle"), 'vehicles.id', "=", "last_stage_vehicle.vehicle_id")
            ->leftJoin(DB::raw("
                (
                    SELECT
                        lo.id AS load_id,
                        lo.transport_identifier AS load_transport_identifier,
                        t.id AS transport_id,
                        t.name AS transport_name
                    FROM
                        loads AS lo
                    INNER JOIN
                        transports AS t ON lo.exit_transport_id = t.id
                ) AS transports_exit
            "), "vehicles.load_id", "=", "transports_exit.load_id")
            ->leftJoin('rules AS shipping_rules', 'vehicles.shipping_rule_id', '=', 'shipping_rules.id')
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
            ->leftJoin($this->getPositionSubQuery(), 'last_movement.id', '=', 'position.movement_id')
            ->leftJoin(DB::raw("({$this->getCarriersSubQuery()}) AS carriers_route_load"), 'vehicles.id', '=', 'carriers_route_load.vehicle_id');

        $this->addFilters();

        return $this->query;
    }

    /**
     * @return void
     */
    private function addFilters(): void
    {
        if ($vins = $this->request->get('vins')) {
            $vinValues = explode(',', $vins['value']);

            if ($vins['filter_type'] === "not_equal") {
                $this->query = $this->query->whereNotIn('vehicles.vin', $vinValues)
                    ->whereNotIn("vehicles.vin_short", $vinValues);
            } else {
                $this->query = $this->query->whereIn('vehicles.vin', $vinValues)
                    ->orWhereIn("vehicles.vin_short", $vinValues);
            }
        }

        if ($stateId = $this->request->get('state_id')) {
            $this->query = $this->query->where('last_state_vehicle.id', $stateId);
        }

        if (($stateDate = $this->request->get('state_date')) && $stateId) {
            $columnStateDate = in_array((int) $stateId, array_keys(self::$statesReferenceColumnToSearch))
                ? self::$statesReferenceColumnToSearch[$stateId]
                : null;

            if ($columnStateDate) {
                list($startStateDate, $endStateDate) = array_values(DatesHelper::getFormattedRangeDates($stateDate));

                $this->query = $this->query->whereBetween("states_dates_vehicle.{$columnStateDate}", [$startStateDate, $endStateDate]);
            }
        }

        if ($rows = $this->request->get('rows')) {
            $rows = explode(',', $rows);

            $this->query = $this->query
                ->whereIn('position.destination_position_row_id', $rows);
        }
    }


    /**
     * @return Expression
     */
    private function getPositionSubQuery(): Expression
    {
        $slotModel = QueryHelper::escapeNamespaceClass(Slot::class);
        $parkingModel = QueryHelper::escapeNamespaceClass(Parking::class);

        $parkingsSubQuery = DB::table("parkings", "p")
            ->select([
                "p.id AS parking_id",
                "p.name AS parking_name",
                "r.id AS row_id",
                "r.row_number AS row_number",
                "s.id AS slot_id",
                "s.slot_number AS slot_number",
            ])
            ->leftJoin("rows AS r", "p.id", "=", "r.parking_id")
            ->leftJoin("slots AS s", "r.id", "=", "s.row_id");

        $query = "SELECT
            lmov2.id AS movement_id,
            lmov2.origin_position_type,
            lmov2.destination_position_type,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_id, parking_origin.id) AS origin_position_id,
            IF(lmov2.origin_position_type = ':slot_model', CONCAT(slot_origin.parking_name, '.', LPAD(slot_origin.row_number, 3, '0')), parking_origin.name) AS origin_position_name,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.parking_id, parking_origin.id) AS origin_position_parking_id,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.parking_name, parking_origin.name) AS origin_position_parking_name,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.row_id, null) AS origin_position_row_id,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.row_number, null) AS origin_position_row_number,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_id, null) AS origin_position_slot_id,
            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_number, null) AS origin_position_slot_number,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_id, parking_destination.id) AS destination_position_id,
            IF(lmov2.destination_position_type = ':slot_model', CONCAT(slot_destination.parking_name, '.', LPAD(slot_destination.row_number, 3, '0')), parking_destination.name) AS destination_position_name,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.parking_id, parking_destination.id) AS destination_position_parking_id,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.parking_name, parking_destination.name) AS destination_position_parking_name,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.row_id, null) AS destination_position_row_id,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.row_number, null) AS destination_position_row_number,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_id, null) AS destination_position_slot_id,
            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_number, null) AS destination_position_slot_number
        FROM
            movements AS lmov2
        LEFT JOIN
         (
            {$parkingsSubQuery->toSql()}
        ) AS slot_origin ON lmov2.origin_position_id = slot_origin.slot_id AND lmov2.origin_position_type = ':slot_model'
        LEFT JOIN
         (
            {$parkingsSubQuery->toSql()}
        ) AS slot_destination ON lmov2.destination_position_id = slot_destination.slot_id AND lmov2.destination_position_type = ':slot_model'
        LEFT JOIN
            parkings AS parking_origin ON lmov2.origin_position_id = parking_origin.id AND lmov2.origin_position_type = ':parking_model'
        LEFT JOIN
            parkings AS parking_destination ON lmov2.destination_position_id = parking_destination.id AND lmov2.destination_position_type = ':parking_model'
        WHERE
            lmov2.confirmed = 1";

        $query = str_replace([":slot_model", ":parking_model"], [$slotModel, $parkingModel], $query);

        return DB::raw("($query) AS position");
    }

    /**
     * @return string
     */
    private function getLastStateVehicleSubQuery(): string
    {
        return "
            SELECT
                states.id,
                states.name,
                states.description,
                last_state.vehicle_id,
                last_state.created_at,
                last_state.dt_announced,
                last_state.dt_terminal,
                last_state.dt_left
            FROM
                states
            INNER JOIN (
                SELECT
                    MAX(vehicles_states.id) AS id,
                    vehicles_states.vehicle_id,
                    MAX(vehicles_states.state_id) AS state_id,
                    MAX(vehicles_states.created_at) AS created_at,
                    MAX(CASE WHEN vehicles_states.state_id = 1 THEN vehicles_states.created_at END) AS dt_announced,
                    MAX(CASE WHEN vehicles_states.state_id = 2 THEN vehicles_states.created_at END) AS dt_terminal,
                    MAX(CASE WHEN vehicles_states.state_id = 3 THEN vehicles_states.created_at END) AS dt_left
                from
                    vehicles_states
                GROUP BY vehicles_states.vehicle_id
            ) AS last_state ON states.id = last_state.state_id
        ";
    }

    private function getLastStageVehicleSubQuery(): string
    {
        return "
            SELECT
                stages.id,
                stages.name,
                stages.description,
                last_stage.vehicle_id,
                last_stage.created_at,
                last_stage.dt_gate_release
            FROM
                stages
            INNER JOIN (
                SELECT
                    MAX(vehicles_stages.id) AS id,
                    vehicles_stages.vehicle_id,
                    MAX(vehicles_stages.stage_id) AS stage_id,
                    MAX(vehicles_stages.created_at) AS created_at,
                    MAX(CASE WHEN vehicles_stages.stage_id = 5 THEN vehicles_stages.created_at END) AS dt_gate_release
                from
                    vehicles_stages
                GROUP BY vehicles_stages.vehicle_id
            ) AS last_stage ON stages.id = last_stage.stage_id
        ";
    }

    private function getCarriersSubQuery(): string
    {
        return "
            SELECT
                vehicles.id AS vehicle_id,
                IF(last_state_vehicle.state_id = 3, carriers_loads.carrier_id, carriers_routes.carrier_id) AS carrier_id,
                IF(last_state_vehicle.state_id = 3, carriers_loads.carrier_name, carriers_routes.carrier_name) AS carrier_name,
                IF(last_state_vehicle.state_id = 3, carriers_loads.carrier_short_name, carriers_routes.carrier_short_name) AS carrier_short_name,
                IF(last_state_vehicle.state_id = 3, carriers_loads.carrier_code, carriers_routes.carrier_code) AS carrier_code,
                IF(last_state_vehicle.state_id = 3, carriers_loads.carrier_belongs_to, carriers_routes.carrier_belongs_to) AS carrier_belongs_to
                -- IF(last_state_vehicle.state_id = 3, IF(carriers_loads.carrier_id IS NOT null, 'load', null), IF(carriers_routes.carrier_id IS NOT null, 'route', null)) AS carrier_belongs_to

            FROM
                vehicles
            INNER JOIN (
                SELECT
                    MAX(id) AS id,
                    vehicle_id,
                    MAX(state_id) AS state_id,
                    MAX(created_at) AS created_at
                FROM
                    vehicles_states
                GROUP BY vehicle_id
            ) AS last_state_vehicle ON vehicles.id = last_state_vehicle.vehicle_id

            LEFT JOIN (
                SELECT
                    vehicles.id,
                    carriers.name AS carrier_name,
                    carriers.id AS carrier_id,
                    carriers.short_name AS carrier_short_name,
                    carriers.code AS carrier_code,
                    'route' AS carrier_belongs_to
                FROM
                    vehicles
                INNER JOIN
                    destination_codes ON vehicles.destination_code_id = destination_codes.id
                INNER JOIN
                    routes ON destination_codes.id = routes.destination_code_id AND routes.route_type_id = 1
                INNER JOIN
                    carriers ON routes.carrier_id = carriers.id
            ) AS carriers_routes ON vehicles.id = carriers_routes.id


            LEFT JOIN (
                SELECT
                    vehicles.id AS vehicle_id,
                    carriers.id AS carrier_id,
                    carriers.name AS carrier_name,
                    carriers.short_name AS carrier_short_name,
                    carriers.code AS carrier_code,
                    'load' AS carrier_belongs_to
                FROM
                    vehicles
                INNER JOIN
                    loads ON vehicles.load_id = loads.id
                INNER JOIN
                    carriers ON loads.carrier_id = carriers.id
            ) AS carriers_loads ON vehicles.id = carriers_loads.vehicle_id
        ";
    }
}
