<?php

namespace App\Repositories\Movement\Builders;

use App\Models\Parking;
use App\Models\Movement;
use App\Helpers\DatesHelper;
use App\Helpers\QueryHelper;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class MovementDatatablesQueryBuilder
{
    /**
     * @var Movement
     */
    private $model;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var EloquentBuilder
     */
    private $query;

    public function __construct(Movement $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * @return EloquentBuilder
     */
    public function getQuery(): EloquentBuilder
    {
        $this->query = $this->model->query()
            ->from(DB::raw("({$this->getFromTable()}) AS movements"))
            ->select([
                "movements.*",
                "vehicles.id AS vehicle_id",
                "vehicles.vin AS vehicle_vin",
                "devices.id AS device_id",
                "devices.name AS device_name",
                "devices.uuid AS device_uuid",
                "users.id AS user_id",
                "users.username AS user_username",
            ])
            ->join("vehicles", "movements.vehicle_id", "=", "vehicles.id")
            ->join("users", "movements.user_id", "=", "users.id")
            ->leftJoin("devices", "movements.device_id", "=", "devices.id");

        $this->addFilters();

        return $this->query;
    }

    /**
     * Agregar filtros a la query según parámetros enviados por la petición.
     *
     * @return void
     */
    private function addFilters(): void
    {
        // Vins filter
        if ($vins = $this->request->get('vins')) {
            $vinValues = explode(',', $vins['value']);

            if ($vins['filter_type'] === "not_equal") {
                $this->query = $this->query->whereNotIn('vehicles.vin', $vinValues);
            } else {
                $this->query = $this->query->whereIn('vehicles.vin', $vinValues);
            }
        }

        // Users filter
        if ($users = $this->request->get('users')) {
            $users = explode(',', $users);

            $this->query = $this->query->whereIn('users.id', $users);
        }

        // Origins parkings filter
        if ($originsParkings = $this->request->get('origins_parkings')) {
            $originsParkings = explode(',', $originsParkings);

            $this->query = $this->query->whereIn('movements.origin_parking_id', $originsParkings);
        }

        // Destinations parkings filter
        if ($destinationsParkings = $this->request->get('destinations_parkings')) {
            $destinationsParkings = explode(',', $destinationsParkings);

            $this->query = $this->query->whereIn('movements.destination_parking_id', $destinationsParkings);
        }

        // Created_at filter
        if ($dates = $this->request->get('created_at')) {
            list($startDate, $endDate) = array_values(DatesHelper::getFormattedRangeDates($dates));

            $this->query = $this->query->whereBetween("movements.created_at", [$startDate, $endDate]);
        }
    }

    /**
     * Obtener query de movements para la claúsula "from" para la consulta principal.
     *
     * @return string
     */
    private function getFromTable(): string
    {
        $slotModel = QueryHelper::escapeNamespaceClass(Slot::class);
        $parkingModel = QueryHelper::escapeNamespaceClass(Parking::class);

        $parkingSubQuery = DB::table("parkings", "p")
            ->select([
                "p.id AS parking_id",
                "p.name AS parking_name",
                "r.id AS row_id",
                "r.row_number AS row_number",
                "s.id AS slot_id",
                "s.slot_number AS slot_number",
                DB::raw("CONCAT(p.name, IF(r.`row_number` IS NOT NULL, CONCAT('.', LPAD(r.`row_number`, 3, '0')), '')) AS parking_row")
            ])
            ->leftJoin("rows AS r", "p.id", "=", "r.parking_id")
            ->leftJoin("slots AS s", "r.id", "=", "s.row_id");

        $query = "
            SELECT
                movements.*,
                IF(movements.origin_position_type = ':slot_model', slots_origin.parking_row, parking_origin.name) AS origin_position_name,
                IF(movements.origin_position_type = ':slot_model', slots_origin.parking_id, parking_origin.id) AS origin_parking_id,
                IF(movements.origin_position_type = ':slot_model', slots_origin.parking_name, parking_origin.name) AS origin_parking_name,
                IF(movements.origin_position_type = ':slot_model', slots_origin.row_id, null) AS origin_row_id,
                IF(movements.origin_position_type = ':slot_model', slots_origin.`row_number`, null) AS origin_row_number,
                IF(movements.origin_position_type = ':slot_model', slots_origin.slot_id, null) AS origin_slot_id,
                IF(movements.origin_position_type = ':slot_model', slots_origin.slot_number, null) AS origin_slot_number,
                IF(movements.destination_position_type = ':slot_model', slots_destination.parking_row, parking_destination.name) AS destination_position_name,
                IF(movements.destination_position_type = ':slot_model', slots_destination.parking_id, parking_destination.id) AS destination_parking_id,
                IF(movements.destination_position_type = ':slot_model', slots_destination.parking_name, parking_destination.name) AS destination_parking_name,
                IF(movements.destination_position_type = ':slot_model', slots_destination.row_id, null) AS destination_row_id,
                IF(movements.destination_position_type = ':slot_model', slots_destination.`row_number`, null) AS destination_row_number,
                IF(movements.destination_position_type = ':slot_model', slots_destination.slot_id, null) AS destination_slot_id,
                IF(movements.destination_position_type = ':slot_model', slots_destination.slot_number, null) AS destination_slot_number,
                (
                    CASE
                        WHEN movements.confirmed = 1 THEN 'confirmed'
                        WHEN movements.canceled = 1 THEN 'canceled'
                        ELSE 'pending'
                    END
                ) AS `status`
            FROM
                movements
            LEFT JOIN
            (
                {$parkingSubQuery->toSql()}
            ) AS slots_origin ON movements.origin_position_id = slots_origin.slot_id and movements.origin_position_type = ':slot_model'
            LEFT JOIN
            (
                {$parkingSubQuery->toSql()}
            ) AS slots_destination ON movements.destination_position_id = slots_destination.slot_id and movements.destination_position_type = ':slot_model'
            LEFT JOIN
                parkings AS parking_origin ON movements.origin_position_id = parking_origin.id AND movements.origin_position_type = ':parking_model'
            LEFT JOIN
                parkings AS parking_destination ON movements.destination_position_id = parking_destination.id AND movements.destination_position_type = ':parking_model'
        ";

        return str_replace([":slot_model", ":parking_model"], [$slotModel, $parkingModel], $query);
    }
}
