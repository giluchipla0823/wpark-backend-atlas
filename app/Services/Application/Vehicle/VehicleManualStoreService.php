<?php

namespace App\Services\Application\Vehicle;

use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Color;
use App\Models\Stage;
use App\Models\State;
use App\Models\Parking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Exceptions\owner\NotFoundException;
use App\Exceptions\owner\BadRequestException;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;

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
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function create(array $params): void
    {
        /* @var Parking $parking */
        $parking = $this->parkingRepository->find($params['parking_id']);

        if (!$parking) {
            throw new NotFoundException("El parking seleccionado no existe.");
        }

        if ($parking->area->zone->id !== Zone::OVERFLOW) {
            throw new BadRequestException("El parking seleccionado debe ser de zona de OVERFLOW.");
        }

        DB::transaction(function() use ($params, $parking) {
            $params['color_id'] = $params['color_id'] ?? Color::COLOR_UNKNOWN_ID;

            $vehicle = $this->vehicleRepository->createManual($params);

            $this->createStateAndStage($vehicle);

            $this->createMovement($vehicle, $parking, $params['created_from']);

            $parking->reserve();
        });
    }

    /**
     * @param Vehicle $vehicle
     * @return void
     */
    private function createStateAndStage(Vehicle $vehicle): void
    {
        $vehicle->stages()->sync([
            Stage::STAGE_GATE_RELEASE_ID => [
                'manual' => true,
                'tracking_date' => null,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
        ]);

        $states = array_flip([State::STATE_ANNOUNCED_ID, State::STATE_ON_TERMINAL_ID]);

        $statesSync = array_map(function () {
            return [
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
        }, $states);

        $vehicle->states()->sync($statesSync);
    }

    /**
     * @param Vehicle $vehicle
     * @param Parking $parking
     * @param string $createdFrom
     * @return void
     */
    private function createMovement(Vehicle $vehicle, Parking $parking, string $createdFrom): void
    {
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
            "comments" => $createdFrom === Vehicle::CREATED_FROM_MOBILE
                ? "Movimiento por defecto creado desde la aplicación móvil"
                : null,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);
    }
}
