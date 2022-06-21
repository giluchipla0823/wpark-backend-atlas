<?php

namespace App\Services\Application\Vehicle;

use App\Exceptions\owner\BadRequestException;
use App\Helpers\StringHelper;
use App\Models\Color;
use App\Models\Design;
use App\Models\DestinationCode;
use App\Models\Parking;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\Movement\MovementRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use Carbon\Carbon;
use Exception;
use App\Models\Stage;
use App\Models\Transport;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Color\ColorRepositoryInterface;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\Vehicle\StageRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Services\External\FreightVerify\FreightVerifyService;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;

class VehicleStageService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var StageRepositoryInterface
     */
    private $stageRepository;

    /**
     * @var DesignRepositoryInterface
     */
    private $designRepository;

    /**
     * @var ColorRepositoryInterface
     */
    private $colorRepository;

    /**
     * @var DestinationCodeRepositoryInterface
     */
    private $destinationCodeRepository;

    /**
     * @var FreightVerifyService
     */
    private $freightVerifyService;
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
        StageRepositoryInterface $stageRepository,
        DesignRepositoryInterface $designRepository,
        ColorRepositoryInterface $colorRepository,
        DestinationCodeRepositoryInterface $destinationCodeRepository,
        FreightVerifyService $freightVerifyService,
        MovementRepositoryInterface $movementRepository,
        ParkingRepositoryInterface $parkingRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->stageRepository = $stageRepository;
        $this->designRepository = $designRepository;
        $this->colorRepository = $colorRepository;
        $this->destinationCodeRepository = $destinationCodeRepository;
        $this->freightVerifyService = $freightVerifyService;
        $this->movementRepository = $movementRepository;
        $this->parkingRepository = $parkingRepository;
    }

    /**
     * @param array $params
     * @return void
     * @throws Exception
     */
    public function vehicleStage(array $params): void
    {
        // Log de petición del servicio
        $this->saveLog("Datos recibidos", $params);

        // Añadir vin del vehículo
        $params['vin'] = $params['pvin'];

        // Extraer y añadir vin_short desde el eoc
        $params['vin_short'] = substr($params['eoc'], 24, 7);

        // Extraer y añadir modelo desde el eoc
        $designCode = substr($params['eoc'], 22, 2);
        $design = $this->designRepository->findBy(['code' => $designCode]);

        // Extraer y añadir color desde el eoc
        $colorCode = substr($params['eoc'], 22, 2) . $params['eoc'][72];
        $color = $this->colorRepository->findBy(['code' => $colorCode]);

        // Añadir código de destino
        // $destination_code = substr($params['eoc'], 9, 2);
        // Se puede sacar el código de destino por el eoc pero lo pasan a parte porque puede no estar actualizado en el eoc
        $destinationCode = $this->destinationCodeRepository->findBy(['code' => trim($params['destination'])]);

        // Añadir método de entrada (Se añade por defecto 1 correspondiente a la factoria)
        $params['entry_transport_id'] = Transport::FACTORY;

        // Comprobación si existe o no el stage
        $stage = $this->stageRepository->findBy(['code'=> $params['station']]);

        if (!$stage) {
            $stages = Stage::all()->pluck("code")->toArray();

            throw new BadRequestException(sprintf(
                "La estación especificada no es válida. Las estaciones válidas son: %s",
                implode(",", $stages)
            ));
        }

        // Comprobación si existe o no el vehículo para crear o actualizar
        $vehicle = $this->vehicleRepository->findBy(['vin' => $params['pvin']]);

        if (!$design || !$color || !$destinationCode) {
            $dataToFind = [
                "modelo" => $design,
                "color" => $color,
                "código de destino" => $destinationCode,
            ];

            $missingData = [];

            foreach ($dataToFind as $key => $value) {
                if (!$value) {
                   $missingData[] = $key;
                }
            }

            // TODO: Sacar errores en excel para importarlos (FASE 2)
            $this->saveLog(
                sprintf(
                    'No se ha encontrado información de %s con el EOC especificado.',
                    StringHelper::replaceLastOccurrence(",", " y", implode(", ", $missingData))
                ),
                array_merge(
                    $params,
                    [
                        'design_code' => $designCode,
                        'color_code' => $colorCode,
                        'destination_code' => $params['destination'],
                        'missing_data' => $missingData
                    ]
                ),
                $vehicle
            );

            $params['design_id'] = Design::UNKNOWN_ID;
            $params['color_id'] = Color::UNKNOWN_ID;
            $params['destination_code_id'] = DestinationCode::UNKNOWN_ID;
        } else {
            $params['design_id'] = $design->id;
            $params['color_id'] = $color->id;
            $params['destination_code_id'] = $destinationCode->id;
        }

        $vehicle = $this->createOrUpdateVehicle($params, $vehicle);

        $body = [];
        $load = $vehicle->loads()->first();
        if ($load) {
            if (isset($load->carrier)) {
                $body['assetId'] = $load->carrier->code;
            }
            if ($load->license_plate) {
                $body['equipmentNumber'] = $load->license_plate;
            }
        }

        if($params['station'] === Stage::STAGE_ST5_CODE) {
            $this->freightVerifyService->sendVehicleReceived(
                $params['vin'],
                array_merge($body, [
                    'transportationType' => Transport::getFreightVerifyType($vehicle->transport->name)
                ]),
                1
            );
            $this->freightVerifyService->sendInspectionCompleted($params['vin'], $body, 1);
        } else if($params['station'] === Stage::STAGE_ST8_CODE) {
            $this->freightVerifyService->sendCompoundExit($params['vin'], $body, 1);
        } elseif ($params['station'] === Stage::STAGE_ST7_CODE) {
            $this->freightVerifyService->sendReleasedToCarrier($params['vin'], $body, 1);
        }
    }

    /**
     * @param array $params
     * @param Vehicle|null $vehicle
     * @return Vehicle
     * @throws Exception
     */
    private function createOrUpdateVehicle(array $params, ?Vehicle $vehicle = null): Vehicle
    {
        $stage = Stage::where('code', $params['station'])->first();

        $isNewRecord = is_null($vehicle);

        // Comprobar si el eoc ya existe cuando se hace un registro
        if ($isNewRecord && $this->vehicleRepository->findBy(['eoc' => $params['eoc']])) {
            $this->saveLog("El eoc especificado ya se encuentra registrado", $params, $vehicle);

            throw new BadRequestException('El eoc especificado ya se encuentra registrado.');
        }

        DB::beginTransaction();

        try {
            if ($isNewRecord) {

                $vehicle = $this->vehicleRepository->create($params);

                $this->createInitialMovement($vehicle, $params);
            } else {
                $this->vehicleRepository->update($params, $vehicle->id);
            }

            $this->saveLog($isNewRecord ? "Vehículo creado" : "Vehículo actualizado", $params, $vehicle);

            $this->updateStageAndStateVehicle($vehicle, $stage, $params);

            DB::commit();
        }catch (Exception $exc) {
            DB::rollback();

            $errorMessage = $isNewRecord ? "Error al crear vehículo" : "Error al actualizar el vehículo";

            $this->saveLog($errorMessage, $params, $vehicle);

            throw new Exception($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $vehicle;
    }

    /**
     * @param string $message
     * @param array $params
     * @param Vehicle|null $vehicle
     * @return void
     */
    private function saveLog(string $message, array $params, ?Vehicle $vehicle = null): void
    {
        $activity = activity('Tracking-point')
            ->withProperties($params)
            ->log($message);

        $activity->reference_code = 'ST7-api';

        if ($vehicle) {
            $activity->subject_type = get_class($vehicle);
            $activity->subject_id = $vehicle->id;
        }

        $activity->save();
    }

    /**
     * @param Vehicle $vehicle
     * @param array $params
     * @return void
     */
    private function createInitialMovement(Vehicle $vehicle, array $params): void
    {
        $canopy = $this->parkingRepository->find(1);

        $this->movementRepository->create([
            "vehicle_id" => $vehicle->id,
            "user_id" => 1,
            "origin_position_type" => Parking::class,
            "origin_position_id" => 0,
            "destination_position_type" => get_class($canopy),
            "destination_position_id" => $canopy->id,
            "category" => null,
            "confirmed" => 1,
            "canceled" => 0,
            "manual" => $params['manual'],
            "dt_start" => Carbon::now(),
            "dt_end" => Carbon::now(),
            "comments" => "Movimiento por defecto creado desde la Api ST7",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);
    }

    /**
     * @param Vehicle $vehicle
     * @param Stage $stage
     * @param array $params
     * @return void
     */
    private function updateStageAndStateVehicle(Vehicle $vehicle, Stage $stage, array $params): void
    {
        /**
         * Vehículo recibe station "03" y no tiene ningún state le asignamos el state ANNOUNCED.
         */
        if ($vehicle->states->count() === 0) {
            $vehicle->states()->sync([
                State::STATE_ANNOUNCED_ID => [
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ]
            ], false);
        }

        $hasOnTerminalState = !is_null($vehicle->states->where('id', State::STATE_ON_TERMINAL_ID)->first());
        $hasCurrentState = !is_null($vehicle->stages->where('id', $stage->id)->first());

        if (
            !$hasOnTerminalState &&
            in_array($stage->code, [Stage::STAGE_ST4_CODE, Stage::STAGE_ST5_CODE, Stage::STAGE_ST6_CODE])
        ) {
            $vehicle->states()->sync([
                State::STATE_ON_TERMINAL_ID => [
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ]
            ], false);
        }

        if (!$hasCurrentState) {
            $vehicle->stages()->sync([
                $stage->id => [
                    'manual' => $params['manual'],
                    'tracking_date' => $params['tracking-date']
                ]
            ], false);
        }
    }
}
