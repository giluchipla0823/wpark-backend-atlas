<?php

namespace App\Services\Application\Movement;

use Exception;
use App\Http\Resources\Movement\MovementFilteredPositionResource;
use App\Http\Resources\Parking\ParkingFilteredPositionResource;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class MovementManualService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    /**
     * @var SlotRepositoryInterface
     */
    private $slotRepository;

    /**
     * @var RowRepositoryInterface
     */
    private $rowRepository;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        ParkingRepositoryInterface $parkingRepository,
        SlotRepositoryInterface $slotRepository,
        RowRepositoryInterface $rowRepository
    ) {
        $this->movementRepository = $movementRepository;
        $this->parkingRepository = $parkingRepository;
        $this->slotRepository = $slotRepository;
        $this->rowRepository = $rowRepository;
    }

    /**
     * @param array $params
     * @return ParkingFilteredPositionResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws Exception
     */
    public function filteredPositions(array $params)
    {

        $vehicle = Vehicle::find($params['vehicle_id']);
        $parking = Parking::find($params['parking_id']);

        // Si el parking seleccionado es de tipo ilimitado devolvemos el parking
        if ($parking->parkingType->id === ParkingType::TYPE_UNLIMITED) {
            return new ParkingFilteredPositionResource($parking);
        }

        /* Obtenemos las filas del parking que cumplan las siguientes condiciones:
            - Debe pertenecer al parking seleccionado
            - Debe ser una fila que no esté llena y esté activa
            - Debe estar asociada a un bloque que contenga la regla de posición final de transporte del vehículo o block_id sea null
            - Debe tener el rule_id null o coincidir con el shipping_rule_id del vehículo
        */
        $ruleVehicle = $vehicle->shippingRule;
        $blocks = $ruleVehicle->blocks->pluck('id');
        $rows = Row::where('parking_id', $parking->id)->where('full', 0)->where('active', 1)->where(function ($query) use ($blocks) {
            $query->whereIn('block_id', $blocks)->orWhereNull('block_id');
        })->where(function ($query) use ($ruleVehicle) {
            $query->where('rule_id', $ruleVehicle->id)->orWhereNull('rule_id');
        })->get();

        // Si hay filas disponibles con esas condiciones se devuelven
        if ($rows->isEmpty()) {
            throw new Exception("No hay filas disponibles para el vehículo en este parking", Response::HTTP_NOT_FOUND);
        }

        return MovementFilteredPositionResource::collection($rows);
    }

}
