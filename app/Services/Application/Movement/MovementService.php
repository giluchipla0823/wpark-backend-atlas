<?php

namespace App\Services\Application\Movement;

use Exception;
use App\Exceptions\owner\BadRequestException;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Movement\MovementRecommendResource;
use App\Http\Resources\Movement\MovementResource;
use App\Http\Resources\Movement\MovementDatatablesResource;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Models\Slot;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Events\CompletedRowNotification;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class MovementService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var SlotRepositoryInterface
     */
    private $slotRepository;

    /**
     * @var RowRepositoryInterface
     */
    private $rowRepository;

    /**
     * @var MovementRecommendService
     */
    private $movementRecommendService;

    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        SlotRepositoryInterface $slotRepository,
        RowRepositoryInterface $rowRepository,
        MovementRecommendService $movementRecommendService,
        ParkingRepositoryInterface $parkingRepository
    ) {
        $this->movementRepository = $movementRepository;
        $this->slotRepository = $slotRepository;
        $this->rowRepository = $rowRepository;
        $this->movementRecommendService = $movementRecommendService;
        $this->parkingRepository = $parkingRepository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->movementRepository->all($request);

        return MovementResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->movementRepository->datatables($request);

        $resource = MovementDatatablesResource::collection($results['data']);

        $results['data'] = $resource->collection->toArray();

        return collect($results);
    }

    /**
     * @param Movement $movement
     * @return MovementResource
     */
    public function show(Movement $movement): MovementResource
    {
        $movement->load(QueryParamsHelper::getIncludesParamFromRequest());

        return new MovementResource($movement);
    }

    /**
     * @param array $params
     * @return Movement
     */
    public function create(array $params): Movement
    {
        $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();

        $params['category'] = $vehicle->shippingRule->name;
        $params['dt_start'] = Carbon::now();

        return $this->movementRepository->create($params);
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->movementRepository->update($params, $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->movementRepository->delete($id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function restore(int $id): void
    {
        $this->movementRepository->restore($id);
    }

    /**
     * Confirmar movimiento
     *
     * @param Movement $movement
     * @return void
     * @throws Exception
     */
    public function confirmMovement(Movement $movement): void
    {
        // Comprobación de que el movimiento está en proceso
        if ($movement->confirmed) {
            throw new Exception('This movement is already confirmed', Response::HTTP_BAD_REQUEST);
        }

        if ($movement->canceled) {
            throw new Exception('This movement is already canceled', Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            // Actualización del movimiento para indicar que el vehículo ya está en esa posición
            $this->movementRepository->update(['confirmed' => 1, 'dt_end' => Carbon::now()], $movement->id);

            // Comprobar que el origen es parking con filas
            if ($movement->origin_position_type === Slot::class) {
                // Actualización de la fila y el slot para desocupar la posición de origen

                $this->slotRepository->update(['fill' => 0, 'fillmm' => 0], $movement->origin_position_id);

                $row = $movement->originPosition->row;
                $parking = $row->parking;

                /*
                $this->rowRepository->update([
                    'rule_id' => $row->fill === 1 ? null : $row->rule_id,
                    'fill' => $parking->parkingType->id === ParkingType::TYPE_ROW ? $row->fill - 1 : 0,
                    'fillmm' => $row->fillmm - $movement->vehicle->design->length
                ], $row->id);
                */

                $row->rule_id = $row->fill === 1 ? null : $row->rule_id;
                $row->save();
                $row->decrement("fill");
                $row->decrement("fillmm", $movement->vehicle->design->length);
                $parking->decrement("fill");
            } else {
                $movement->originPosition->decrement("fill");
            }

            // Comprobar que el destino es parking con filas
            if ($movement->destination_position_type === Slot::class) {
                $row = $movement->destinationPosition->row;
                $parking = $row->parking;
                $sender = $movement->user;

                /* Calcular espacio en mm restantes para saber si cabe otro vehículo.
                En caso de no caber indicar que la fila está completa con el campo full
                */
                $rowTotalCapacitymm = $row->capacitymm - $row->fillmm;

                if ($rowTotalCapacitymm < Slot::CAPACITY_MM) {
                    $this->rowRepository->update(['full' => 1], $row->id);

                    // Lanzamos la notificación de fila completada
                    event(new CompletedRowNotification($sender, $row));
                }

                $parkingCapacity = $parking->capacity - $parking->fill;

                if ($parkingCapacity === 0){
                    $this->parkingRepository->update(['full' => 1], $parking->id);

                    // TODO: Crear evento de notificación para parking completo
                }
            }

//            // Comprobar si el parking está completo. Solo para posiciones destino de tipo "Slot"
//            if ($movement->destination_position_type === Slot::class) {
//                $parkingCapacity = $movement->destinationPosition->row->parking->capacity - $movement->destinationPosition->row->parking->fill;
//                if ($parkingCapacity === 0){
//                    $parkingParams = [
//                        'full' => 1
//                    ];
//                    $this->parkingRepository->update($parkingParams, $movement->destinationPosition->row->parking->id);
//
//                    // TODO: Crear evento de notificación para parking completo
//                }
//            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }
    }

    /**
     * Cancelar movimiento.
     *
     * @param array $params
     * @param Movement $movement
     * @return void
     * @throws Exception
     */
    public function cancelMovement(array $params, Movement $movement): void
    {
        // Comprobación de que el movimiento está en proceso
        if ($movement->canceled) {
            throw new Exception('This movement is already canceled', Response::HTTP_BAD_REQUEST);
        }

        if ($movement->confirmed) {
            throw new Exception('This movement is already confirmed', Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            // Actualización del movimiento para indicar que el movimiento se ha cancelado
            $MovementParams = [
                'canceled' => 1,
                'comments' => isset($params['comments']) ? $params['comments'] : null
            ];
            $this->movementRepository->update($MovementParams, $movement->id);

            // Comprobar que el destino es parking con filas
            if ($movement->destination_position_type === Slot::class) {

                /* Se desocupa la posición guardada al crear el movimiento y si ocupaba la primera
                posición también se vuelve a poner el rule_id de la fila en null */
                $slotParams = [
                    'fill' => 0,
                    'fillmm' => 0
                ];
                $this->slotRepository->update($slotParams, $movement->destinationPosition->id);

                $rowParams = [
                    'rule_id' => $movement->destinationPosition->row->fill === 1 ? null : $movement->destinationPosition->row->rule_id,
                    'fill' => $movement->destinationPosition->row->parking->parkingType->id === ParkingType::TYPE_ROW ? $movement->destinationPosition->row->fill - 1 : 0,
                    'fillmm' => $movement->destinationPosition->row->fillmm - $movement->vehicle->design->length,
                    'full' => $movement->destinationPosition->row->parking->parkingType->id === ParkingType::TYPE_ROW ? $movement->destinationPosition->row->full : 0,
                ];
                $this->rowRepository->update($rowParams, $movement->destinationPosition->row_id);

                $parkingParams = [
                    'fill' => $movement->destinationPosition->row->parking->fill - 1
                ];
                $this->parkingRepository->update($parkingParams, $movement->destinationPosition->row->parking->id);

            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }
    }

    /**
     * Reload Movimiento
     *
     * @param array $params
     * @return MovementRecommendResource
     * @throws Exception
     */
    public function reload(array $params): MovementRecommendResource
    {

        $previousMovement = Movement::find($params['previous_movement_id']);
        $this->cancelMovement($params, $previousMovement);
        return $this->movementRecommendService->movement($params);
    }

    /**
     * Movimiento Manual.
     *
     * @param array $params
     * @return void
     * @throws BadRequestException
     */
    public function manual(array $params): void
    {
        /* @var Vehicle $vehicle */
        $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();

        $lastConfirmedMovement = $vehicle->lastMovement;

        if (!$lastConfirmedMovement) {
            throw new BadRequestException("El vehículo no tiene movimientos anteriores confirmados.");
        }

        if (
            $lastConfirmedMovement->destination_position_type !== $params['origin_position_type'] ||
            $lastConfirmedMovement->destination_position_id !== $params['origin_position_id']
        ) {
            throw new BadRequestException(
                "La posición actual del vehículo no coincide con la posición de origen especificada."
            );
        }

        if(
            $params['origin_position_type'] === $params['destination_position_type'] &&
            $params['origin_position_id'] === $params['destination_position_id']
        ){
            throw new BadRequestException("La posición de origen y destino no pueden ser iguales.");
        }

        $params['user_id'] = Auth::user()->id;
        $params['manual'] = 1;
        $params['dt_start'] = Carbon::now();

        $ruleVehicle = null;

        // Si la posición de destino es un parking se comprueba si está activo y sigue sin estar lleno
        if ($params['destination_position_type'] === Parking::class) {
            $params['category'] = null;
            $positionDestination = Parking::where('id', $params['destination_position_id'])->first();

            if ($positionDestination->full) {
                throw new BadRequestException("El parking seleccionado está completo.");
            }

            if (!$positionDestination->active) {
                throw new BadRequestException("El parking seleccionado no está activo.");
            }
        } else {
            $params['category'] = $vehicle->shippingRule->name;
            $positionDestination = Slot::where('id', $params['destination_position_id'])->first();

            // Validación de la regla del vehículo con el bloque de la fila
            $ruleVehicle = $vehicle->shippingRule;
            $blocks = $ruleVehicle->blocks->pluck('id');

            $rowBlockValidate = Row::where('id', $positionDestination->row->id)
                                    ->whereIn('block_id', $blocks)
                                    ->orWhereNull('block_id')
                                    ->exists();

            if (!$rowBlockValidate) {
                throw new BadRequestException("La posición seleccionada no coincide con la regla de final de transporte del vehículo");
            }

            // Validación de fila que no esté llene y esté activa, además verifica la regla de transporte del vehículo con la regla de la fila
            $rowAvailableValidate = Row::where('id', $positionDestination->row->id)
                                        ->where([
                                            ['full', "=", 0],
                                            ['active', "=", 1],
                                        ])
                                        ->where(function ($query) use ($ruleVehicle) {
                                            $query->where('rule_id', $ruleVehicle->id)->orWhereNull('rule_id');
                                        })->exists();

            // Añadimos la comprobación de que el slot siga vacio
            if (!$rowAvailableValidate || $positionDestination->fill === 1) {
                throw new BadRequestException("La posición seleccionada no se encuentra disponible.");
            }
        }

        DB::transaction(function() use ($params, $vehicle, $positionDestination, $ruleVehicle) {
            $movement = $this->movementRepository->create($params);
            $vehicleLength = $vehicle->design->length;

            if ($params['destination_position_type'] === Slot::class) {
                // Se reserva la posición del vehículo ocupándola en la base de datos
                $slot = $positionDestination;
                $slot->increment("fill");
                $slot->increment("fillmm", $vehicleLength);

                $row = $slot->row;
                $parking = $row->parking;

                $row->rule_id = $row->rule_id ?: $ruleVehicle->id;
                $row->full = $parking->isRowType() ? $row->full : 1;
                $row->save();
                $row->increment("fill");
                $row->increment("fillmm", $vehicleLength);
            } else {
                $parking = $positionDestination;
            }

            $parking->increment('fill');

            $this->confirmMovement($movement);
        });
    }
}
