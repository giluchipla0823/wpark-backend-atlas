<?php

namespace App\Services\Application\Movement;

use App\Http\Resources\Movement\MovementFilteredPositionResource;
use App\Http\Resources\Parking\ParkingFilteredPositionResource;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Models\Movement;
use App\Models\Slot;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
     * @return MovementFilteredPositionResource|ParkingFilteredPositionResource
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

    /**
     * @param array $params
     * @return Movement
     */
    public function manual(array $params): Movement
    {
        $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();

        $params['user_id'] = Auth::user()->id;
        $params['manual'] = 1;
        $params['category'] = $vehicle->shippingRule->name;
        $params['dt_start'] = Carbon::now();

        if($params['origin_position_type'] === $params['destination_position_type']){
            if($params['origin_position_id'] === $params['destination_position_id']){
                throw new Exception("La posición de origen y destino no pueden ser iguales", Response::HTTP_BAD_REQUEST);
            }
        }
        // Si la posición de destino es un parking se comprueba si está activo y sigue sin estar lleno
        if ($params['destination_position_type'] === Parking::class) {
            $parkingDestination = Parking::where('id', $params['destination_position_id'])->first();
            if ($parkingDestination->full) {
                throw new Exception("El parking seleccionado está completo", Response::HTTP_BAD_REQUEST);
            }
            if (!$parkingDestination->active) {
                throw new Exception("El parking seleccionado no está activo", Response::HTTP_BAD_REQUEST);
            }
        } else {
            $slotDestination = Slot::where('id', $params['destination_position_id'])->first();
            // Validación de la regla del vehículo con el bloque de la fila
            $ruleVehicle = $vehicle->shippingRule;
            $blocks = $ruleVehicle->blocks->pluck('id');

            $rowBlockValidate = Row::where('id', $slotDestination->row->id)->whereIn('block_id', $blocks)->orWhereNull('block_id')->exists();

            if (!$rowBlockValidate) {
                throw new Exception("La posición seleccionada no coincide con la regla de final de transporte del vehículo", Response::HTTP_BAD_REQUEST);
            }

            // Validación de fila que no esté llene y esté activa, además verifica la regla de transporte del vehículo con la regla de la fila
            $rowDisponibleValidate = Row::where('id', $slotDestination->row->id)->where('full', 0)->where('active', 1)->where(function ($query) use ($ruleVehicle) {
                $query->where('rule_id', $ruleVehicle->id)->orWhereNull('rule_id');
            })->exists();

            // Añadimos la comprobación de que el slot siga vacio
            if (!$rowDisponibleValidate || $slotDestination->fill === 1) {
                throw new Exception("La posición seleccionada no se encuentra disponible", Response::HTTP_BAD_REQUEST);
            }
        }

        DB::beginTransaction();

        try {

            $movement = $this->movementRepository->create($params);

            // Se reserva la posición del vehículo ocupándola en la base de datos
            $slotParams = [
                'fill' => 1,
                'fillmm' => $vehicle->design->length
            ];
            $this->slotRepository->update($slotParams, $slotDestination->id);

            // Comprobamos si el parking es de tipo fila o espiga
            $rowType = $slotDestination->row->parking->parking_type_id === ParkingType::TYPE_ROW ? true : false;

            $rowParams = [
                'rule_id' => $slotDestination->row->rule_id ? $slotDestination->row->rule_id : $ruleVehicle->id,
                'fill' => $rowType ? $slotDestination->row->fill + 1 : 1,
                'fillmm' => $slotDestination->row->fillmm + $vehicle->design->length,
                'full' => $rowType ? $slotDestination->row->full : 1
            ];

            $this->rowRepository->update($rowParams, $slotDestination->row->id);

            $parkingParams = [
                'fill' => $slotDestination->row->parking->fill + 1
            ];
            $this->parkingRepository->update($parkingParams, $slotDestination->row->parking->id);

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return $movement;
    }
}
