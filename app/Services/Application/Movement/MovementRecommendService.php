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
            // Cogemos el last_rule_id del vehículo que es el que vamos a usar para presorting
            $ruleVehicle = $vehicle->lastRule;

            // Lógica de recomendación de movimientos
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
            // Cogemos el shipping_rule_id del vehículo que es el que vamos a usar para posición final de transporte
            $ruleVehicle = $vehicle->shippingRule;

            // Lógica de recomendación de movimientos
            $positionRecommend = $this->recommend($vehicle, $ruleVehicle, $params, true);

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return new MovementRecommendResource($positionRecommend);
    }

    /**
     * Recomendación de movimientos.
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

        // Buscamos las filas que a través de los bloques tengan asociada esa regla
        if($finalPosition){
            $blocks = $rule->blocks->where('is_presorting', 0)->pluck('id');
        }else{
            $blocks = $rule->blocks->where('is_presorting', 1)->pluck('id');
        }

        $exceptRows = array_unique($params['except_rows'] ?? []);

        $queryRowsMatch = Row::where('active', 1)->whereIn('block_id', $blocks);

        if (count($exceptRows) > 0) {
            $queryRowsMatch = $queryRowsMatch->whereNotIn('id', $exceptRows);
        }

        /* @var \Illuminate\Database\Eloquent\Collection $rowsMatch */
        $rowsMatch = $queryRowsMatch->orderBy('parking_id', 'ASC')->orderBy('id')->get();

        // Primero comprobamos si alguna de esas filas está empezada, no completada y comparte la misma regla
        // $row->capacitymm - $row->fillmm
        $rowRecommend = $rowsMatch->where('fill', '>', 0)
                            ->where('full', false)
                            ->where('rule_id', $rule->id)
                            ->filter(function($row) {
                                return ($row->capacitymm - $row->fillmm) >= Slot::CAPACITY_MM;
                            })
                            ->first();

        // Si no hay filas empezadas, cogemos la primera fila que no esté llena y no esté empezada
        if (!$rowRecommend) {
            $rowRecommend = $rowsMatch->where('fill', 0)->first();
        }

        if (!$rowRecommend) {
            throw new BadRequestException("No se encontró una posición para recomendar.", [
                'do_manual_recommendation' => true
            ]);
        }

        // Comprobamos si el parking es de tipo fila o espiga
        $rowType = $rowRecommend->parking->parking_type_id === ParkingType::TYPE_ROW;

        // En caso de ser de tipo fila sacaremos la información del último vehículo colocado en esa fila
        $lastVehicle = null;

        if ($rowType) {

            // De la fila vemos cuantos slots están ocupados y elegimos el siguiente
            $recommend = Slot::where('row_id', $rowRecommend->id)->where('slot_number', ($rowRecommend->fill + 1))->first();

            // Para obtener los datos del vehículo en la última posición de la fila
            $vehiclesInRow = $this->vehicleRepository->findAllByRow($rowRecommend);

            $lastVehicle = $vehiclesInRow->last();
        } else {
            $recommend = Slot::where('row_id', $rowRecommend->id)->first();
        }

        if (!$recommend) {
            throw new BadRequestException("No se encontró una posición para recomendar.", [
                'do_manual_recommendation' => true
            ]);
        }

        $positionRecommend = collect(['position' => $recommend])->merge(['vehicle' => $lastVehicle]);

        $comments = $params['comments'] ?? null;

        // Se crea el movimiento
        $movement = $this->movementRepository->create([
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::user()->id,
            'origin_position_type' => $vehicle->lastMovement->destination_position_type,
            'origin_position_id' => $vehicle->lastMovement->destination_position_id,
            'destination_position_type' => Slot::class,
            'destination_position_id' => $positionRecommend['position']->id,
            'category' => $rule->name,
            'dt_start' => Carbon::now(),
            'comments' => $comments
        ]);

        $vehicleLength = $vehicle->design->length;

        // Se reserva la posición del vehículo (slot, row y parking)
        /* @var Slot $slot */
        $slot = $this->slotRepository->find($positionRecommend['position']->id);

        $slot->reserve($vehicleLength);

        $row = $slot->row;
        $row->rule_id = $rowRecommend->rule_id ?: $rule->id;
        $row->save();

        $positionRecommend->put("movement", $movement);

        return $positionRecommend;
    }
}
