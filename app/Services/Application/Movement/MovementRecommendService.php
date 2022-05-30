<?php

namespace App\Services\Application\Movement;

use App\Http\Resources\Movement\MovementRecommendResource;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\ParkingType;
use App\Models\Row;
use App\Models\Rule;
use App\Models\Slot;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
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

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        VehicleRepositoryInterface $vehicleRepository,
        SlotRepositoryInterface $slotRepository,
        RowRepositoryInterface $rowRepository
    ) {
        $this->movementRepository = $movementRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->slotRepository = $slotRepository;
        $this->rowRepository = $rowRepository;
    }

    public function movement(Array $params)
    {
        $vehicle = Vehicle::find($params['vehicle_id']);
        $action = $params['action'];

        switch ($action) {
            case Movement::MOVEMENT_ACTION_OK:
                $response = $this->okMovement($vehicle);
                break;
            case Movement::MOVEMENT_ACTION_NO_OK:
                // Funcionalidad Fase 2
                break;
            case Movement::MOVEMENT_ACTION_SALES:
                // Funcionalidad Fase 2
                break;
            case Movement::MOVEMENT_ACTION_ESCAPE:
                $response = $this->escapeMovement($vehicle);
                break;
        }
        return $response;
    }

    /**
     * @param Vehicle $vehicle
     * @return MovementRecommendResource
     */
    private function okMovement(Vehicle $vehicle)
    {
        DB::beginTransaction();

        try {
            // Cogemos el last_rule_id del vehículo que es el que vamos a usar para presorting
            $ruleVehicle = $vehicle->lastRule;

            // Lógica de recomendación de movimientos
            $positionRecommend = $this->recommend($vehicle, $ruleVehicle, false);

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
     * @return MovementRecommendResource
     */
    private function escapeMovement(Vehicle $vehicle)
    {
        DB::beginTransaction();

        try {
            // Cogemos el shipping_rule_id del vehículo que es el que vamos a usar para posición final de transporte
            $ruleVehicle = $vehicle->shippingRule;

            // Lógica de recomendación de movimientos
            $positionRecommend = $this->recommend($vehicle, $ruleVehicle, true);

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return new MovementRecommendResource($positionRecommend);
    }

    /**
     * @param Vehicle $vehicle
     * @param Rule $rule
     * @param bool $finalPosition
     * @return Collection
     */
    private function recommend(Vehicle $vehicle, Rule $rule, bool $finalPosition)
    {

        // Buscamos las filas que a través de los bloques tengan asociada esa regla
        if($finalPosition){
            $blocks = $rule->blocks->where('is_presorting', 0)->pluck('id');
        }else{
            $blocks = $rule->blocks->where('is_presorting', 1)->pluck('id');
        }

        $rowMatch = Row::whereIn('block_id', $blocks)->orderBy('parking_id', 'ASC')->orderBy('id')->get();

        // Primero comprobamos si alguna de esas filas está empezada, no completada y comparte la misma regla
        $rowRecommend = $rowMatch->where('fill', '>', 0)->where('full', false)->where('rule_id', $rule->id)->first();

        // Si no hay filas empezadas, cogemos la primera fila que no esté llena y no esté empezada
        if (!$rowRecommend) {
            $rowRecommend = $rowMatch->where('fill', 0)->first();
        }

        // Comprobamos si el parking es de tipo fila o espiga
        $rowType = $rowRecommend->parking->parking_type_id === ParkingType::TYPE_ROW ? true : false;

        // En caso de ser de tipo fila sacaremos la información del último vehículo colocado en esa fila
        if ($rowType) {

            // De la fila vemos cuantos slots están ocupados y elegimos el siguiente
            $positionRecommend = Slot::where('row_id', $rowRecommend->id)->where('slot_number', ($rowRecommend->fill + 1))->first();

            // Para obtener los datos del vehículo en la última posición de la fila
            $vehiclesInRow = $this->vehicleRepository->findAllByRow($rowRecommend);
            $lastVehicleInRow = $vehiclesInRow->last();
            $positionRecommend = collect(['position' => $positionRecommend])->merge(['vehicle' => $lastVehicleInRow]);
        } else {
            $positionRecommend = collect(['position' => Slot::where('row_id', $rowRecommend->id)->first()]);
        }

        // Se crea el movimiento
        $movementParams = [
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::user()->id,
            'origin_position_type' => !$vehicle->lastMovement ? Parking::class : $vehicle->lastMovement->destination_position_type,
            'origin_position_id' => !$vehicle->lastMovement ? 1 : $vehicle->lastMovement->destination_position_id,
            'destination_position_type' => Slot::class,
            'destination_position_id' => $positionRecommend['position']->id,
            'category' => $rule->name,
            'dt_start' => Carbon::now()
        ];

        $this->movementRepository->create($movementParams);

        // Se reserva la posición del vehículo ocupándola en la base de datos
        $slotParams = [
            'fill' => 1,
            'fillmm' => $vehicle->design->length
        ];
        $this->slotRepository->update($slotParams, $positionRecommend['position']->id);

        $rowParams = [
            'rule_id' => $rowRecommend->rule_id ? $rowRecommend->rule_id : $rule->id,
            'fill' => $rowType ? $rowRecommend->fill + 1 : 1,
            'fillmm' => $rowRecommend->fillmm + $vehicle->design->length
        ];

        $this->rowRepository->update($rowParams, $positionRecommend['position']->row_id);

        return $positionRecommend;
    }
}