<?php

namespace App\Services\Application\Movement;

use App\Exceptions\owner\NotFoundException;
use App\Helpers\RowHelper;
use Exception;
use App\Exceptions\owner\BadRequestException;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Movement\MovementRecommendResource;
use App\Http\Resources\Movement\MovementResource;
use App\Http\Resources\Movement\MovementDatatablesResource;
use App\Models\Movement;
use App\Models\Parking;
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
     * @throws Exception
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
     * @param Movement $movement
     * @param bool $mustReleaseOrigin
     * @return void
     * @throws Exception
     */
    public function confirmMovement(Movement $movement, bool $mustReleaseOrigin = true): void
    {
        // Comprobaci??n de que el movimiento est?? en proceso
        if ($movement->confirmed) {
            throw new Exception('This movement is already confirmed', Response::HTTP_BAD_REQUEST);
        }

        if ($movement->canceled) {
            throw new Exception('This movement is already canceled', Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            // Actualizaci??n del movimiento para indicar que el veh??culo ya est?? en esa posici??n
            $this->movementRepository->update(['confirmed' => 1, 'dt_end' => Carbon::now()], $movement->id);

            if ($mustReleaseOrigin) {
                // Comprobar que el origen es parking con filas
                if ($movement->originPositionIsSlot()) {
                    // Actualizaci??n de la fila y el slot para desocupar la posici??n de origen

                    /* @var Slot $slot */
                    $slot = $movement->originPosition;
                    $slot->release($movement->vehicle->design->length);

                    $row = $slot->row;

                    $parking = $row->parking;
                } else {
                    /* @var Parking $parking */
                    $parking = $movement->originPosition;
                    $parking->release();
                }

                $parkingCapacity = $parking->capacity - $parking->fill;

                if ($parkingCapacity > 0){
                    $parking->full = 0;
                    $parking->save();
                }
            }

            // Comprobar que el destino es parking con filas
            if ($movement->destinationPositionIsSlot()) {
                $row = $movement->destinationPosition->row;
                $parking = $row->parking;
                $sender = $movement->user;

                /* Calcular espacio en mm restantes para saber si cabe otro veh??culo.
                En caso de no caber indicar que la fila est?? completa con el campo full
                */
                $rowTotalCapacitymm = $row->capacitymm - $row->fillmm;

                if ($rowTotalCapacitymm < Slot::CAPACITY_MM) {
                    $row->full = 1;
                    $row->save();

                    // Lanzamos la notificaci??n de fila completada
                    event(new CompletedRowNotification($sender, $row));
                }

                $parkingCapacity = $parking->capacity - $parking->fill;

                if ($parkingCapacity === 0){
                    $parking->full = 1;
                    $parking->save();

                    // TODO: Crear evento de notificaci??n para parking completo
                }
            }

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
        // Comprobaci??n de que el movimiento est?? en proceso
        if ($movement->canceled) {
            return;
        }

        if ($movement->confirmed) {
            throw new BadRequestException('This movement is already confirmed');
        }

        DB::beginTransaction();

        try {
            // Actualizaci??n del movimiento para indicar que el movimiento se ha cancelado
            $this->movementRepository->update([
                'canceled' => 1,
                'comments' => $params['comments'] ?? null
            ], $movement->id);

            // Comprobar que el destino es slot.
            if ($movement->destinationPositionIsSlot()) {

                /* @var Slot $slot */
                $slot = $movement->destinationPosition;

                $slot->release($movement->vehicle->design->length);

//                /* @var Row $row */
//                $row = $slot->row;

//                if ($row->fill === 0) {
//                    $row->full = 0;
//                    $row->save();
//                }
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

        $this->cancelMovement(array_merge($params, ["comments" => "Cancelado por reload"]), $previousMovement);

        return $this->movementRecommendService->movement($params);
    }

    /**
     * Movimiento Manual.
     *
     * @param array $params
     * @return void
     * @throws NotFoundException
     * @throws Exception
     */
    public function manual(array $params): void
    {
        DB::transaction(function () use ($params) {
            $previousMovementId = $params['cancel_previous_movement_id'] ?? null;

            if ($previousMovementId) {
                $previousMovement = Movement::find($previousMovementId);

                if (!$previousMovement) {
                    throw new NotFoundException(sprintf(
                        "No existe informaci??n del movimiento anterior con el id %s especificado."
                    ), $previousMovementId);
                }

//                $canCancelMovement = true;
//
//                if (
//                    $previousMovement->destination_position_type ===  $params['destination_position_type'] &&
//                    $previousMovement->destination_position_id === $params['destination_position_id']
//                ) {
//                    $canCancelMovement = false;
//                }

                $this->cancelMovement(['comments' => "Cancelado por movimiento manual"], $previousMovement);
            }

            /* @var Vehicle $vehicle */
            $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();

            $this->checkValidateVehicleCurrentPosition($vehicle, [
                "id" => $params['origin_position_id'],
                "type" => $params['origin_position_type'],
            ]);

            $params['manual'] = 1;

            $movement = $this->doMovement($vehicle, $params);

            $this->confirmMovement($movement);
        });
    }

    /**
     * @param Vehicle $vehicle
     * @param array $params
     * @return Movement
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function doMovement(Vehicle $vehicle, array $params): Movement
    {
        $params['user_id'] = Auth::user()->id;
        $params['dt_start'] = Carbon::now();
        $forceMovement = $params['force_movement'] ?? false;

        $ruleVehicle = null;

        // Si la posici??n de destino es un parking se comprueba si est?? activo y sigue sin estar lleno
        if ($params['destination_position_type'] === Parking::class) {
            $params['category'] = null;
            $positionDestination = Parking::where('id', $params['destination_position_id'])->first();

            if ($positionDestination->full) {
                throw new BadRequestException("El parking seleccionado est?? completo.");
            }

            if (!$positionDestination->active) {
                throw new BadRequestException("El parking seleccionado no est?? activo.");
            }
        } else {
            $positionDestination = Slot::where('id', $params['destination_position_id'])->first();

            // A??adimos la comprobaci??n de que el slot siga vacio
            // if ($positionDestination->fill === 1) {
            if ($positionDestination->real_fill === 1) {
                throw new BadRequestException("La posici??n seleccionada no se encuentra disponible.");
            }

            $row = $positionDestination->row;

            $rowIsPresortingZone = RowHelper::isPresortinZone($row);

            // Validaci??n de la regla del veh??culo con el bloque de la fila
            $ruleVehicle = $rowIsPresortingZone ? $vehicle->lastRule : $vehicle->shippingRule;

            if (!$row->active) {
                throw new BadRequestException("La fila de la posici??n seleccionada no est?? activa.");
            }

            if ($row->full) {
                throw new BadRequestException("La fila de la posici??n seleccionada est?? llena.");
            }

            if (!$ruleVehicle) {
                if ($rowIsPresortingZone) {
                    throw new BadRequestException(
                        "El veh??culo no tiene asignado una regla de presorting para la posici??n seleccionada."
                    );
                } else {
                    throw new BadRequestException(
                        "El veh??culo no tiene asignado una regla de posici??n final de transporte para la posici??n seleccionada."
                    );
                }
            }

            if (!$forceMovement) {
                if ($row->category && $row->category !== $ruleVehicle->name) {
                    throw new BadRequestException(sprintf(
                        "El veh??culo no cumple con la regla %s que tiene asignada la fila de la posici??n seleccionada.",
                        $row->category
                    ));
                }

                $blocks = $ruleVehicle->blocks->pluck('id');

                $rowBlockValidate = Row::where('id', $row->id)
                    ->whereIn('block_id', $blocks)
                    ->orWhereNull('block_id')
                    ->exists();

                if (!$rowBlockValidate) {
                    if ($rowIsPresortingZone) {
                        throw new BadRequestException("El veh??culo no cumple con la regla(s) de presorting del bloque que tiene asignado la fila de la posici??n seleccionada.");
                    } else {
                        throw new BadRequestException("El veh??culo no cumple con la regla(s) de posici??n final de transporte del bloque que tiene asignado la fila de la posici??n seleccionada.");
                    }
                }
            }

            $params['category'] = $ruleVehicle->name;
        }

        $movement = $this->movementRepository->create($params);

        if ($params['destination_position_type'] === Slot::class) {
            // Se reserva la posici??n del veh??culo ocup??ndola en la base de datos

            /* @var Slot $slot */
            $slot = $positionDestination;
            $slot->reserve($vehicle->design->length);
            $row = $slot->row;
            $parking = $row->parking;
            $row->category = $row->category ?: $ruleVehicle->name;
            $row->full = $parking->isRowType() ? $row->full : 1;
            $row->save();
        } else {
            /* @var Parking $parking */
            $parking = $positionDestination;
            $parking->reserve();
        }

        return $movement;
    }

    /**
     * Verifica si la posici??n de destino del ??ltimo movimiento confirmado del veh??culo
     * coincide con la posici??n de origen especificada.
     *
     * @throws BadRequestException
     */
    public function checkValidateVehicleCurrentPosition(Vehicle $vehicle, array $originPosition): void
    {
        $lastConfirmedMovement = $vehicle->lastConfirmedMovement;

        if (!$lastConfirmedMovement) {
            throw new BadRequestException("El veh??culo no tiene movimientos anteriores confirmados.");
        }

        if (
            $lastConfirmedMovement->destination_position_type !== $originPosition['type'] ||
            $lastConfirmedMovement->destination_position_id !== $originPosition['id']
        ) {
            throw new BadRequestException(
                "La posici??n actual del veh??culo {$vehicle->vin} no coincide con la posici??n de origen especificada."
            );
        }
    }
}
