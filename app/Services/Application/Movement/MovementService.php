<?php

namespace App\Services\Application\Movement;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Movement\MovementResource;
use App\Http\Resources\Movement\MovementDatatablesResource;
use App\Models\Movement;
use App\Models\ParkingType;
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
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Services\Application\Movement\MovementRecommendService;

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
        ParkingRepositoryInterface $parkingRepository,
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

    public function restore(int $id): void
    {
        $this->movementRepository->restore($id);
    }

    /**
     * @param Movement $movement
     * @return void
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
            $MovementParams = [
                'confirmed' => 1,
                'dt_end' => Carbon::now()
            ];
            $this->movementRepository->update($MovementParams, $movement->id);

            // Comprobar que el origen es parking con filas
            if ($movement->origin_position_type === Slot::class) {
                // Actualización de la fila y el slot para desocupar la posición de origen
                $slotParams = [
                    'fill' => 0,
                    'fillmm' => 0
                ];
                $this->slotRepository->update($slotParams, $movement->origin_position_id);

                $rowParams = [
                    'rule_id' => $movement->originPosition->row->fill === 1 ? null : $movement->originPosition->row->rule_id,
                    'fill' => $movement->originPosition->row->parking->parkingType->id === ParkingType::TYPE_ROW ? $movement->originPosition->row->fill - 1 : 0,
                    'fillmm' => $movement->originPosition->row->fillmm - $movement->vehicle->design->length
                ];
                $this->rowRepository->update($rowParams, $movement->originPosition->row_id);
            }

            // Comprobar que el destino es parking con filas
            if ($movement->destination_position_type === Slot::class) {
                /* Calcular espacio en mm restantes para saber si cabe otro vehículo.
                En caso de no caber indicar que la fila está completa con el campo full
                */
                $rowTotalCapacitymm = $movement->destinationPosition->row->capacitymm - $movement->destinationPosition->row->fillmm;

                if ($rowTotalCapacitymm < Slot::CAPACITY_MM) {
                    $rowParams = [
                        'full' => 1,
                    ];
                    $this->rowRepository->update($rowParams, $movement->destinationPosition->row_id);

                    // Lanzamos la notificación de fila completada
                    $row = $movement->destinationPosition->row;

                    $sender = $movement->user;

                    event(new CompletedRowNotification($sender, $row));
                }
            }

            // Comprobar si el parking está completo
            $parkingCapacity = $movement->destinationPosition->row->parking->capacity - $movement->destinationPosition->row->parking->fill;
            if ($parkingCapacity === 0){
                $parkingParams = [
                    'full' => 1
                ];
                $this->parkingRepository->update($parkingParams, $movement->destinationPosition->row->parking->id);

                // TODO: Crear evento de notificación para parking completo
            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }
    }

    /**
     * @param Movement $movement
     * @param array $params
     * @return void
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
     * @param array $params
     * @return Collection
     */
    public function reload(array $params){

        $previousMovement = Movement::find($params['previous_movement_id']);
        $this->cancelMovement($params, $previousMovement);
        return $this->movementRecommendService->movement($params);
    }
}
