<?php

namespace App\Services\Application\Vehicle;

use App\Models\Stage;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleManualStoreService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;
    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        MovementRepositoryInterface  $movementRepository,
        ParkingRepositoryInterface $parkingRepository
    )
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->movementRepository = $movementRepository;
        $this->parkingRepository = $parkingRepository;
    }

    /**
     * @param array $params
     * @return void
     */
    public function create(array $params): void
    {
        DB::transaction(function() use ($params) {
            $vehicle = $this->vehicleRepository->createManual($params);

            $this->createStateAndStage($vehicle);

            $this->createMovement($vehicle, $params);
        });
    }

    /**
     * @param Vehicle $vehicle
     * @return void
     */
    private function createStateAndStage(Vehicle $vehicle): void
    {
        $vehicle->stages()->sync([
            Stage::STAGE_VEHICLE_CREATED_ID => [
                'manual' => true,
                'tracking_date' => null,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
        ]);

        $vehicle->states()->sync([
            State::STATE_ANNOUNCED_ID => [
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ]);
    }

    /**
     * @param Vehicle $vehicle
     * @param array $params
     * @return void
     */
    private function createMovement(Vehicle $vehicle, array $params): void
    {
        $parking = $this->parkingRepository->find($params['position']['id']);

        $this->movementRepository->create([
            "vehicle_id" => $vehicle->id,
            "user_id" => auth()->user()->id,
            "origin_position_type" => get_class($parking),
            "origin_position_id" => 0,
            "destination_position_type" => get_class($parking),
            "destination_position_id" => $parking->id,
            "category" => null,
            "confirmed" => 1,
            "canceled" => 0,
            "manual" => 1,
            "dt_start" => Carbon::now(),
            "dt_end" => Carbon::now(),
            "comments" => $params['created_from'] === Vehicle::CREATED_FROM_MOBILE
                ? "Movimiento por defecto creado desde la aplicación móvil"
                : null,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);
    }
}
