<?php

namespace App\Services\Application\Movement;

use App\Exceptions\owner\BadRequestException;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\Row;
use App\Models\Slot;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MovementRectificationService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var MovementService
     */
    private $movementService;

    public function __construct(
        MovementRepositoryInterface $movementRepository
    )
    {
        $this->movementRepository = $movementRepository;

        $this->movementService = app()->make(MovementService::class);
    }

    /**
     * @throws BadRequestException
     */
    public function process(Movement $movement, array $params)
    {
        $rectificationBy = $params['rectification_by'];
        $forceMovement = $rectificationBy['force_movement'] ?? false;

        switch ($rectificationBy['type']) {
            case Vehicle::class:
                $vehicle = Vehicle::find($rectificationBy['id']);

                if ($vehicle->inMovement()) {
                    throw new BadRequestException(sprintf(
                        "El vehículo con vin %s se encuentra actualmente en movimiento.",
                        $vehicle->vin
                    ));
                }

                $lastConfirmedMovement = $vehicle->lastConfirmedMovement;

                if (!$lastConfirmedMovement) {
                    throw new BadRequestException(sprintf(
                        "El vehículo con vin %s no tiene ningun movimiento confirmado.",
                        $vehicle->vin
                    ));
                }

                if ($lastConfirmedMovement->destination_position_type !== Slot::class) {
                    throw new BadRequestException(sprintf(
                        "El vehículo con vin %s no se encuentra actualmente en ninguna fila.",
                        $vehicle->vin
                    ));
                }

                $currentSlot = $lastConfirmedMovement->destinationPosition;

                $row = $currentSlot->row;

//                if (!$row->active) {
//                    throw new BadRequestException('La fila de la posición seleccionada no está activa');
//                }
//
//                if ($row->full || ())
//
                $nextSlot = $currentSlot->next();

                // $slot = $row->slots->where('real_fill', 0)->first();

                if (!$nextSlot || $nextSlot->real_fill) {
                    throw new BadRequestException("La posición seleccionada no se encuentra disponible.");
                }

                // dd($nextSlot);

                $this->doProcess($movement, $nextSlot, $forceMovement);

                break;

            case Row::class:
                $row = Row::find($rectificationBy['id']);

                // TODO: Comprobar que el primer slot de la fila está vacío
                $slot = $row->slots->first();

                if ($slot->lastConfirmedDestinationMovement && !$slot->lastConfirmedDestinationMovement->vehicle->inMovement()) {
                    throw new BadRequestException("La primera posición de la fila no está disponible.");
                }

                // dd($slot->lastConfirmedDestinationMovement);

                // Obtenemos los vehículos en movimiento
                $vehiclesInMovement = $row->slots
                        ->filter(function($item) {
                            $lastConfirmedMovement = $item->lastConfirmedDestinationMovement;

                            return (
                                $lastConfirmedMovement &&
                                $lastConfirmedMovement->destination_position_id === $item->id &&
                                $lastConfirmedMovement->vehicle->inMovement()
                            );
                        })
                        ->map(function($item) {
                            return $item->lastConfirmedDestinationMovement->vehicle;
                        });

                // dd($vehiclesInMovement);

                // Obtenemos la última posición pendiente de confirmar de cada vehículo
                $pendingMovementsVehiclesInRoute = $vehiclesInMovement->map(function($vehicle) {
                    return $vehicle->lastPendingMovement;
                });

                // dd($pendingMovementsVehiclesInRoute->pluck('id'));

                // De todos los movientos pendientes de vehículos en movimiento creamos el movimiento a buffer.
                foreach ($pendingMovementsVehiclesInRoute as $pendingMovement) {
                    // $this->generateMovementToPendingMovement($pendingMovement);
                }


                // if ($slot->fill === 1 && $slot->real_fill === 0) {

                    // TODO: Hay vehículos que están posicionados en la fila y necesitamos cambiar su posición destino

                    // throw new BadRequestException("La posición seleccionada no se encuentra disponible.");
                // }

                // dd($slot);

                // TODO: Tratar esta mierda
                if (count($pendingMovementsVehiclesInRoute) > 0) {
                    throw new BadRequestException('Ocurrió un problema inesperado al reubicar el vehículo en la fila seleccionada');
                }

                $row = Row::find($rectificationBy['id']);

                // TODO: Comprobar que el primer slot de la fila está vacío
                $slot = $row->slots->first();

                // dd($slot);

                $this->doProcess($movement, $slot, $forceMovement);

                break;

            default:
                // TODO: Agregar error porque no existe el tipo de rectificación de movimiento
                break;
        }

    }

    private function doProcess(Movement $movement, Slot $slot, bool $forceMovement)
    {
        DB::transaction(function() use ($movement, $slot, $forceMovement) {
            // Cancelar el movimiento rectificado
            $this->movementService->cancelMovement(['comments' => 'Canceled by movement rectification'], $movement);

            /**
             * TODO:
             * - Verificar vehículos que están en movimiento
             * - Cancelar movimientos actuales de estos vehículos
             * - Generar nuevos movimientos con posición de origen BUFFER hacia la posición a la que iban
             */

            // Crear movimiento automático.
            $originPosition = $movement->originPosition;
            $vehicle = $movement->vehicle;

            $movement = $this->movementService->doMovement($vehicle, [
                "vehicle_id" => $vehicle->id,
                "origin_position_type" => get_class($originPosition),
                "origin_position_id" => $originPosition->id,
                "destination_position_type" => get_class($slot),
                "destination_position_id" => $slot->id,
                "manual" => 0,
                "comments" => "Movement by rectification",
                "force_movement" => $forceMovement
            ]);

            // Confirmar movimiento
            $this->movementService->confirmMovement($movement);
        });
    }


    private function generateMovementToPendingMovement(Movement $pendingMovement) {
        $originPosition = $pendingMovement->destinationPosition;
        $vehicle = $pendingMovement->vehicle;

        // Cancelar el movimiento movimiento pendiente actual
        $this->movementService->cancelMovement(['comments' => 'Canceled by movement rectification - vehicle in movement'], $pendingMovement);

        // Crear movimiento automático para BUFFER
        $buffer = Parking::find(2);

        $movement = $this->movementService->doMovement($vehicle, [
            "vehicle_id" => $vehicle->id,
            "origin_position_type" => get_class($originPosition),
            "origin_position_id" => $originPosition->id,
            "destination_position_type" => get_class($buffer),
            "destination_position_id" => $buffer->id,
            "manual" => 0,
            "comments" => "Movement by rectification to BUFFER",
        ]);

        // Confirmar movimiento para BUFFER
        $this->movementService->confirmMovement($movement, false);

        // Crear movimiento para posición destino antes de crear movimiento para buffer.
        Movement::create([
            "vehicle_id" => $vehicle->id,
            'user_id' => 1,
            'device_id' => 1,
            "origin_position_type" => get_class($buffer),
            "origin_position_id" => $buffer->id,
            "destination_position_type" => get_class($originPosition),
            "destination_position_id" => $originPosition->id,
            "manual" => 0,
            "confirmed" => 0,
            "canceled" => 0,
            "dt_start" => Carbon::now(),
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
            "comments" => "Movement by rectification to {$originPosition->row->row_name}",
        ]);
    }

    private function oldCode(Movement $movement, array $params) {
        $destinationPosition = $params['destination_position'];

        // Cancelar el movimiento rectificado
        $this->movementService->cancelMovement(['comments' => 'Canceled by movement rectification'], $movement);

        /**
         * TODO:
         * - Verificar vehículos que están en movimiento
         * - Cancelar movimientos actuales de estos vehículos
         * - Generar nuevos movimientos con posición de origen BUFFER hacia la posición a la que iban
         */

        // Crear movimiento automático.
        $originPosition = $movement->originPosition;
        $vehicle = $movement->vehicle;

        $movement = $this->movementService->doMovement($vehicle, [
            "vehicle_id" => $vehicle->id,
            "origin_position_type" => get_class($originPosition),
            "origin_position_id" => $originPosition->id,
            "destination_position_type" => $destinationPosition['type'],
            "destination_position_id" => $destinationPosition['id'],
            "manual" => 0,
            "comments" => "Movement by rectification"
        ]);

        // Confirmar movimiento
        $this->movementService->confirmMovement($movement);
    }
}
