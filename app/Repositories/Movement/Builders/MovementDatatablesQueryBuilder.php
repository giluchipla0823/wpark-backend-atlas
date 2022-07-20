<?php

namespace App\Repositories\Movement\Builders;

use App\Models\Parking;
use App\Models\Movement;
use App\Helpers\DatesHelper;
use App\Helpers\QueryHelper;
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
        $parkingModel = QueryHelper::escapeNamespaceClass(Parking::class);

        // Creamos la subquery de parkings.
        $subQuery = $this->getSubQueryParkings();

        $this->query = $this->model->query()
                        ->select([
                            "movements.*",
                            DB::raw("
                            (
                                CASE
                                    WHEN movements.confirmed = 1 THEN 'confirmed'
                                    WHEN movements.canceled = 1 THEN 'canceled'
                                    ELSE 'pending'
                                END
                            ) AS `status`
                            "),
                            "vehicles.id AS vehicle_id",
                            "vehicles.vin AS vehicle_vin",
                            "devices.id AS device_id",
                            "devices.name AS device_name",
                            "devices.uuid AS device_uuid",
                            "users.id AS user_id",
                            "users.username AS user_username",
                            "parking_origin.parking_row AS origin_position_name",
                            "parking_origin.parking_id AS origin_parking_id",
                            "parking_origin.parking_name AS origin_parking_name",
                            "parking_origin.row_id AS origin_row_id",
                            "parking_origin.row_number AS origin_row_number",
                            "parking_origin.slot_id AS origin_slot_id",
                            "parking_origin.slot_number AS origin_slot_number",
                            "parking_destination.parking_row AS destination_position_name",
                            "parking_destination.parking_id AS destination_parking_id",
                            "parking_destination.parking_name AS destination_parking_name",
                            "parking_destination.row_id AS destination_row_id",
                            "parking_destination.row_number AS destination_row_number",
                            "parking_destination.slot_id AS destination_slot_id",
                            "parking_destination.slot_number AS destination_slot_number"
                        ])
                        ->join("vehicles", "movements.vehicle_id", "=", "vehicles.id")
                        ->join("users", "movements.user_id", "=", "users.id")
                        ->leftJoin("devices", "movements.device_id", "=", "devices.id")
                        ->leftJoin(DB::raw("({$subQuery->toSql()}) AS parking_origin"), "movements.origin_position_id", "=", DB::raw("IF (movements.origin_position_type = '{$parkingModel}', parking_origin.parking_id, parking_origin.slot_id)"))
                        ->leftJoin(DB::raw("({$subQuery->toSql()}) AS parking_destination"), "movements.destination_position_id", "=", DB::raw("IF (movements.destination_position_type = '{$parkingModel}', parking_destination.parking_id, parking_destination.slot_id)"));

        $this->addFilters();

        return $this->query;
    }

    /**
     * @return QueryBuilder
     */
    private function getSubQueryParkings(): QueryBuilder
    {
        return DB::table("parkings", "p")
            ->select([
                "p.id AS parking_id",
                "p.name AS parking_name",
                "r.id AS row_id",
                "r.row_number AS row_number",
                "s.id AS slot_id",
                "s.slot_number AS slot_number",
                DB::raw("CONCAT(p.name, IF(r.row_number IS NOT NULL, CONCAT('.', LPAD(r.row_number, 3, '0')), '')) AS parking_row"),
                DB::raw("CONCAT(p.name, IF(r.row_number IS NOT NULL, CONCAT('.', LPAD(r.row_number, 3, '0')), ''), IF(s.id IS NOT NULL, CONCAT('-', s.slot_number), '')) AS parking_row_slot")
            ])
            ->leftJoin("rows AS r", "p.id", "=", "r.parking_id")
            ->leftJoin("slots AS s", "r.id", "=", "s.row_id");
    }

    /**
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

            $this->query = $this->query->whereIn('parking_origin.parking_id', $originsParkings);
        }

        // Destinations parkings filter
        if ($destinationsParkings = $this->request->get('destinations_parkings')) {
            $destinationsParkings = explode(',', $destinationsParkings);

            $this->query = $this->query->whereIn('parking_destination.parking_id', $destinationsParkings);
        }

        // Created_at filter
        if ($dates = $this->request->get('created_at')) {
            list($startDate, $endDate) = array_values(DatesHelper::getFormattedRangeDates($dates));

            $this->query = $this->query->whereBetween("movements.created_at", [$startDate, $endDate]);
        }
    }
}
