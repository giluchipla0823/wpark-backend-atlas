<?php

namespace App\Services\Application\Movement;

use App\Exceptions\owner\BadRequestException;
use App\Http\Resources\Movement\MovementRecommendResource;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Models\Rule;
use App\Models\Slot;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovementRecommendService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;

    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var SlotRepositoryInterface
     */
    private $slotRepository;

    /**
     * @var RowRepositoryInterface
     */
    private $rowRepository;

    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        VehicleRepositoryInterface $vehicleRepository,
        SlotRepositoryInterface $slotRepository,
        RowRepositoryInterface $rowRepository,
        ParkingRepositoryInterface $parkingRepository
    ) {
        $this->movementRepository = $movementRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->slotRepository = $slotRepository;
        $this->rowRepository = $rowRepository;
        $this->parkingRepository = $parkingRepository;
    }

    /**
     * @param array $params
     * @return MovementRecommendResource
     * @throws Exception
     */
    public function movement(Array $params): MovementRecommendResource
    {
        $vehicle = Vehicle::find($params['vehicle_id']);
        $action = $params['action'];

        switch ($action) {
            case Movement::MOVEMENT_ACTION_OK:
                $response = $this->okMovement($vehicle, $params);
                break;
            case Movement::MOVEMENT_ACTION_NO_OK:
                // Funcionalidad Fase 2
                break;
            case Movement::MOVEMENT_ACTION_SALES:
                // Funcionalidad Fase 2
                break;
            case Movement::MOVEMENT_ACTION_ESCAPE:
                $response = $this->escapeMovement($vehicle, $params);
                break;
        }
        return $response;
    }

    /**
     * @param Vehicle $vehicle
     * @param array $params
     * @return MovementRecommendResource
     * @throws BadRequestException
     */
    private function okMovement(Vehicle $vehicle, array $params): MovementRecommendResource
    {
        DB::beginTransaction();

        try {
            // Cogemos el last_rule_id del veh??culo que es el que vamos a usar para presorting
            $ruleVehicle = $vehicle->lastRule;

            // L??gica de recomendaci??n de movimientos
            $positionRecommend = $this->recommend($vehicle, $ruleVehicle, $params, false);

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return new MovementRecommendResource($positionRecommend);
    }

    private function noOkMovement()
    {
        // Funcionalidad Fase 2
    }

    private function salesMovement()
    {
        // Funcionalidad Fase 2
    }

    /**
     * @param Vehicle $vehicle
     * @param array $params
     * @return MovementRecommendResource
     * @throws BadRequestException
     */
    private function escapeMovement(Vehicle $vehicle, array $params): MovementRecommendResource
    {
        DB::beginTransaction();

        try {
            // Cogemos el shipping_rule_id del veh??culo que es el que vamos a usar para posici??n final de transporte
            $ruleVehicle = $vehicle->shippingRule;

            // L??gica de recomendaci??n de movimientos
            $positionRecommend = $this->recommend($vehicle, $ruleVehicle, $params, true);

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return new MovementRecommendResource($positionRecommend);
    }

    /**
     * Recomendaci??n de movimientos.
     *
     * @param Vehicle $vehicle
     * @param Rule $rule
     * @param array $params
     * @param bool $finalPosition
     * @return Collection
     * @throws BadRequestException
     */
    private function recommend(Vehicle $vehicle, Rule $rule, array $params, bool $finalPosition)
    {
        // Buscamos las filas que a trav??s de los bloques tengan asociada esa regla
        $is_presorting = $finalPosition ? 0 : 1;

        $blocks = $rule->blocks->where('is_presorting', $is_presorting)->where('active', 1)->pluck('id');

        $exceptRows = array_unique($params['except_rows'] ?? []);

        $queryRowsMatch = Row::where('active', 1)->whereIn('block_id', $blocks);

        if (count($exceptRows) > 0) {
            $queryRowsMatch = $queryRowsMatch->whereNotIn('id', $exceptRows);
        }

        /* @var Collection $rowsMatch */
        $rowsMatch = $queryRowsMatch->orderBy('parking_id', 'ASC')->orderBy('id')->get();

        // Primero comprobamos si alguna de esas filas est?? empezada, no completada y comparte la misma regla
        $rowRecommend = $rowsMatch->where('fill', '>', 0)
                            ->where('full', false)
                            ->where('category', $rule->name)
                            ->filter(function($row) {
                                return ($row->capacitymm - $row->fillmm) >= Slot::CAPACITY_MM;
                            })
                            ->first();

        // Si no hay filas empezadas, cogemos la primera fila que no est?? llena y no est?? empezada
        if (!$rowRecommend) {
            $rowRecommend = $rowsMatch->where('fill', 0)->first();
        }

        if (!$rowRecommend) {
            throw new BadRequestException("No se encontr?? una posici??n para recomendar.", [
                'do_manual_recommendation' => true
            ]);
        }

        // Comprobamos si el parking es de tipo fila o espiga
        $lastVehicle = null;

        if ($rowRecommend->parking->isRowType()) {

            // De la fila vemos cuantos slots est??n ocupados y elegimos el siguiente
            // $recommend = Slot::where('row_id', $rowRecommend->id)->where('slot_number', ($rowRecommend->fill + 1))->first();
            // $recommend = Slot::where('row_id', $rowRecommend->id)->where('fill', 0)->first();
            $recommend = Slot::where('row_id', $rowRecommend->id)->where('fill', 0)->first();

            /**
             * En caso de ser de tipo fila sacaremos la informaci??n del ??ltimo veh??culo colocado en esa fila.
             * Para obtener los datos del veh??culo en la ??ltima posici??n de la fila.
             */
            $vehiclesInRow = $this->vehicleRepository->findAllByRow($rowRecommend);

            $lastVehicle = $vehiclesInRow->last();
        } else {
            $recommend = Slot::where('row_id', $rowRecommend->id)->first();
        }

        if (!$recommend) {
            throw new BadRequestException("No se encontr?? una posici??n para recomendar.", [
                'do_manual_recommendation' => true
            ]);
        }

        $positionRecommend = collect(['position' => $recommend])->merge(['vehicle' => $lastVehicle]);

        $comments = $params['comments'] ?? null;

        // $originPositionId = $vehicle->lastConfirmedMovement->destination_position_id;
        // $originPositionType = $vehicle->lastConfirmedMovement->destination_position_type;

        $originPosition = $vehicle->lastConfirmedMovement->destinationPosition;

        /**
         * Si hay nueva recomendaci??n de movimiento, comprobamos si el veh??culo viene de un slot de fila y
         * se verifica que no se tenga una notificaci??n
         *
         */
        if (get_class($originPosition) === Slot::class) {

        }

        // Se crea el movimiento
        $movement = $this->movementRepository->create([
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::user()->id,
//            'origin_position_type' => $originPositionType,
//            'origin_position_id' => $originPositionId,
            'origin_position_type' => get_class($originPosition),
            'origin_position_id' => $originPosition->id,
            'destination_position_type' => Slot::class,
            'destination_position_id' => $positionRecommend['position']->id,
            'category' => $rule->name,
            'dt_start' => Carbon::now(),
            'comments' => $comments
        ]);

        $vehicleLength = $vehicle->design->length;

        // Se reserva la posici??n del veh??culo (slot, row y parking)
        /* @var Slot $slot */
        $slot = $this->slotRepository->find($positionRecommend['position']->id);

        $slot->reserve($vehicleLength);

        $row = $slot->row;
        $row->category = $rowRecommend->category ?: $rule->name;
        $row->save();

        $positionRecommend->put("movement", $movement);

        return $positionRecommend;
    }
}
