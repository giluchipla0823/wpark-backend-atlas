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
        $parkingModel = QueryHelper::escapeNamespaceClass(Parking::class);

        $this->query = DB::table($this->model->getTable())
            ->select([
                "vehicles.id",
                "vehicles.vin",
                "vehicles.vin_short",
                "vehicles.info",
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
                "states_dates_vehicle.dt_announced",
                "states_dates_vehicle.dt_terminal",
                "states_dates_vehicle.dt_left",
                "stages_dates_vehicle.dt_gate_release",
                "transports_entry.id AS transport_entry_id",
                "transports_entry.name AS transport_entry_name",
                "transports_exit.transport_id AS transport_exit_id",
                "transports_exit.transport_name AS transport_exit_name",
                "stages.id AS stage_id",
                "stages.name AS stage_name",
                "stages.description AS stage_description",
                "position.*"
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
            ->leftJoin(DB::raw("({$this->getStatesDatesVehicleSubQuery()}) AS states_dates_vehicle"), "vehicles.id", "=", "states_dates_vehicle.vehicle_id")
            ->leftJoin(DB::raw("({$this->getLastStateVehicleSubQuery()}) AS last_state_vehicle"), 'vehicles.id', "=", "last_state_vehicle.vehicle_id")
            ->leftJoin('states', 'last_state_vehicle.state_id', '=', 'states.id')
            ->leftJoin(DB::raw("({$this->getStagesDatesVehicleSubQuery()}) AS stages_dates_vehicle"), "vehicles.id", "=", "stages_dates_vehicle.vehicle_id")
            ->leftJoin(DB::raw("({$this->getLastStageVehicleSubQuery()}) AS last_stage_vehicle"), 'vehicles.id', "=", "last_stage_vehicle.vehicle_id")
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
            ->leftJoin($this->getPositionSubQuery(), 'last_movement.id', '=', 'position.movement_id');

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
            $this->query = $this->query->where('states.id', $stateId);
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


//        $query = "SELECT
//            lmov2.id AS movement_id,
//            lmov2.origin_position_type,
//            lmov2.destination_position_type,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_id, parking_origin.id) AS origin_position_id,
//            IF(lmov2.origin_position_type = ':slot_model', CONCAT(slot_origin.parking_name, '.', LPAD(slot_origin.row_number, 3, '0')), parking_origin.name) AS origin_position_name,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.parking_id, parking_origin.id) AS origin_position_parking_id,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.parking_name, parking_origin.name) AS origin_position_parking_name,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.row_id, null) AS origin_position_row_id,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.row_number, null) AS origin_position_row_number,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_id, null) AS origin_position_slot_id,
//            IF(lmov2.origin_position_type = ':slot_model', slot_origin.slot_number, null) AS origin_position_slot_number,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_id, parking_destination.id) AS destination_position_id,
//            IF(lmov2.destination_position_type = ':slot_model', CONCAT(slot_destination.parking_name, '.', LPAD(slot_destination.row_number, 3, '0')), parking_destination.name) AS destination_position_name,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.parking_id, parking_destination.id) AS destination_position_parking_id,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.parking_name, parking_destination.name) AS destination_position_parking_name,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.row_id, null) AS destination_position_row_id,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.row_number, null) AS destination_position_row_number,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_id, null) AS destination_position_slot_id,
//            IF(lmov2.destination_position_type = ':slot_model', slot_destination.slot_number, null) AS destination_position_slot_number
//        FROM
//            movements AS lmov2
//        LEFT JOIN
//         (
//            SELECT
//                park.id AS parking_id,
//                park.name AS parking_name,
//                r.id AS row_id,
//                r.row_number AS row_number,
//                slot.id AS slot_id,
//                slot.slot_number AS slot_number
//            FROM
//                slots AS slot
//                INNER JOIN `rows` AS r on slot.row_id = r.id
//                INNER JOIN parkings AS park on r.parking_id = park.id
//        ) AS slot_origin ON lmov2.origin_position_id = slot_origin.slot_id AND lmov2.origin_position_type = ':slot_model'
//        LEFT JOIN
//         (
//            SELECT
//                park.id AS parking_id,
//                park.name AS parking_name,
//                r.id AS row_id,
//                r.row_number AS row_number,
//                slot.id AS slot_id,
//                slot.slot_number AS slot_number
//            FROM
//                slots AS slot
//                INNER JOIN `rows` AS r on slot.row_id = r.id
//                INNER JOIN parkings AS park on r.parking_id = park.id
//        ) AS slot_destination ON lmov2.destination_position_id = slot_destination.slot_id AND lmov2.destination_position_type = ':slot_model'
//        LEFT JOIN
//            parkings AS parking_origin ON lmov2.origin_position_id = parking_origin.id AND lmov2.origin_position_type = ':parking_model'
//        LEFT JOIN
//            parkings AS parking_destination ON lmov2.destination_position_id = parking_destination.id AND lmov2.destination_position_type = ':parking_model'
//        WHERE
//            lmov2.confirmed = 1";

        $query = str_replace([":slot_model", ":parking_model"], [$slotModel, $parkingModel], $query);


        return DB::raw("($query) AS position");
    }

    /**
     * @return string
     */
    private function getStatesDatesVehicleSubQuery(): string
    {
        return DB::table("vehicles_states")
            ->select([
                "vehicle_id",
                DB::raw("MAX(CASE WHEN state_id = ". State::STATE_ANNOUNCED_ID ." THEN created_at END) AS dt_announced"),
                DB::raw("MAX(CASE WHEN state_id = ". State::STATE_ON_TERMINAL_ID ." THEN created_at END) AS dt_terminal"),
                DB::raw("MAX(CASE WHEN state_id = ". State::STATE_LEFT_ID ." THEN created_at END) AS dt_left")
            ])
            ->groupBy("vehicle_id")
            ->toSql();
    }

    /**
     * @return string
     */
    private function getLastStateVehicleSubQuery(): string
    {
        return DB::table("vehicles_states")
                ->select([
                    DB::raw("MAX(id) AS id"),
                    "vehicle_id",
                    DB::raw("MAX(state_id) AS state_id"),
                    DB::raw("MAX(created_at) AS created_at"),
                ])
                ->groupBy("vehicle_id")
                ->toSql();

    }

    private function getStagesDatesVehicleSubQuery(): string
    {
        return DB::table("vehicles_stages")
                ->select([
                    "vehicle_id",
                    DB::raw("MAX(CASE WHEN stage_id = ". Stage::STAGE_GATE_RELEASE_ID ." THEN created_at END) AS dt_gate_release")
                ])
                ->groupBy("vehicle_id")
                ->toSql();
    }

    private function getLastStageVehicleSubQuery(): string
    {
        return DB::table("vehicles_stages")
            ->select([
                DB::raw("MAX(id) AS id"),
                "vehicle_id",
                DB::raw("MAX(stage_id) AS stage_id"),
                DB::raw("MAX(created_at) AS created_at"),
            ])
            ->groupBy("vehicle_id")
            ->toSql();
    }
}
