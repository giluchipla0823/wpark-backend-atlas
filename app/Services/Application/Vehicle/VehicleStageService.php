<?php

namespace App\Services\Application\Vehicle;

use Exception;
use Carbon\Carbon;
use App\Models\Stage;
use App\Models\Color;
use App\Models\State;
use App\Models\Dealer;
use App\Models\Design;
use App\Models\Parking;
use App\Models\Vehicle;
use App\Models\Transport;
use App\Models\ActivityLog;
use App\Helpers\StringHelper;
use App\Models\DestinationCode;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\FORD\FordStandardErrorException;
use App\Repositories\Color\ColorRepositoryInterface;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\Vehicle\StageRepositoryInterface;
use App\Repositories\Dealer\DealerRepositoryInterface;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Repositories\Movement\MovementRepositoryInterface;
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
    /**
     * @var DealerRepositoryInterface
     */
    private $dealerRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        StageRepositoryInterface $stageRepository,
        DesignRepositoryInterface $designRepository,
        ColorRepositoryInterface $colorRepository,
        DestinationCodeRepositoryInterface $destinationCodeRepository,
        FreightVerifyService $freightVerifyService,
        MovementRepositoryInterface $movementRepository,
        ParkingRepositoryInterface $parkingRepository,
        DealerRepositoryInterface $dealerRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->stageRepository = $stageRepository;
        $this->designRepository = $designRepository;
        $this->colorRepository = $colorRepository;
        $this->destinationCodeRepository = $destinationCodeRepository;
        $this->freightVerifyService = $freightVerifyService;
        $this->movementRepository = $movementRepository;
        $this->parkingRepository = $parkingRepository;
        $this->dealerRepository = $dealerRepository;
    }

    /**
     * @param array $params
     * @return void
     * @throws FordStandardErrorException
     * @throws GuzzleException
     */
    public function vehicleStage(array $params): void
    {
        try {
            // Log de petición del servicio
            $this->saveActivityLog("Datos recibidos", $params);

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

            // Dealer
            $dealer = $this->dealerRepository->find(Dealer::UNKNOWN_ID);

            // Añadir método de entrada (Se añade por defecto 1 correspondiente a la factoria)
            $params['entry_transport_id'] = Transport::TRANSPORT_FACTORY_ID;
            $params['dealer_id'] = $dealer->id;

            // Comprobación si existe o no el stage
            $stage = $this->stageRepository->findBy(['code'=> $params['station']]);

            if (!$stage) {
                $stages = Stage::all()->pluck("code")->toArray();

                $this->saveActivityLog(
                    sprintf(
                        "La estación especificada no es válida. Las estaciones válidas son: %s",
                        implode(",", $stages)
                    ),
                    $params
                );

                throw new FordStandardErrorException(
                    [
                        sprintf(
                            "The specified station does not exist. Available stations are: %s",
                            implode(",", $stages)
                        )
                    ],
                    Response::HTTP_NOT_FOUND
                );
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
                $this->saveActivityLog(
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

            $this->callFreightVerifyService($vehicle, $params);
        } catch (Exception $exc) {
            if ($exc instanceof FordStandardErrorException) {
                throw $exc;
            }

            throw new FordStandardErrorException(
                ["An unexpected problem occurred in the execution of the service."],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Crear o actualizar información del vehículo.
     *
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
            $this->saveActivityLog("El eoc especificado ya se encuentra registrado", $params, $vehicle);

            throw new FordStandardErrorException(
                ["The specified eoc is already registered."],
                Response::HTTP_BAD_REQUEST
            );
        }

        DB::beginTransaction();

        try {
            if ($isNewRecord) {

                $vehicle = $this->vehicleRepository->create($params);

                $this->createInitialMovement($vehicle, $params);
            } else {
                $this->vehicleRepository->update($params, $vehicle->id);
            }

            $this->saveActivityLog($isNewRecord ? "Vehículo creado" : "Vehículo actualizado", $params, $vehicle);

            $this->updateStageAndStateVehicle($vehicle, $stage, $params);

            DB::commit();
        }catch (Exception $exc) {
            DB::rollback();

            $this->saveActivityLog(
                $isNewRecord ? "Error al crear vehículo" : "Error al actualizar el vehículo",
                $params,
                $vehicle
            );

            throw new FordStandardErrorException(
                [$isNewRecord ? "Error when creating the vehicle" : "Error updating vehicle data"],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $vehicle;
    }

    /**
     * Guardar log de cada suceso realizado.
     *
     * @param string $message
     * @param array $params
     * @param Vehicle|null $vehicle
     * @return void
     */
    private function saveActivityLog(string $message, array $params, ?Vehicle $vehicle = null): void
    {
        $activity = activity('Tracking-point')
            ->withProperties($params)
            ->log($message);

        $activity->reference_code = ActivityLog::REFERENCE_CODE_ST7;

        if ($vehicle) {
            $activity->subject_type = get_class($vehicle);
            $activity->subject_id = $vehicle->id;
        }

        $activity->save();
    }

    /**
     * Crea el movimiento inicial(por defecto) del vehículo.
     *
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
        ]);
    }

    /**
     * Actualiza el stage y state del vehiculo según la estación enviada.
     *
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
            $vehicle->states()->sync([State::STATE_ANNOUNCED_ID], false);
        }

        $hasOnTerminalState = !is_null($vehicle->states->where('id', State::STATE_ON_TERMINAL_ID)->first());
        $hasCurrentState = !is_null($vehicle->stages->where('id', $stage->id)->first());

        if (
            !$hasOnTerminalState &&
            in_array($stage->code, [Stage::STAGE_ST4_CODE, Stage::STAGE_ST5_CODE, Stage::STAGE_ST6_CODE])
        ) {
            $vehicle->states()->sync([State::STATE_ON_TERMINAL_ID], false);
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

    /**
     * LLamadas a servicio FreightVerify según la estación.
     *
     * @param Vehicle $vehicle
     * @param array $params
     * @return void
     * @throws GuzzleException
     */
    private function callFreightVerifyService(Vehicle $vehicle, array $params): void
    {
        $body = [];
        $load = $vehicle->loads()->first();

        if ($load) {
            if ($load->carrier) {
                $body['assetId'] = $load->carrier->code;
            }
            if ($load->license_plate) {
                $body['equipmentNumber'] = $load->license_plate;
            }
        }

        $station = $params['station'];
        $vin = $params['vin'];

        switch ($station) {
            case Stage::STAGE_ST5_CODE:
                $this->freightVerifyService->sendVehicleReceived(
                    $vin,
                    array_merge($body, [
                        'transportationType' => Transport::getFreightVerifyType($vehicle->transport->name)
                    ]),
                    1
                );
                $this->freightVerifyService->sendInspectionCompleted($vin, $body, 1);
                break;

            case Stage::STAGE_ST7_CODE:
                $this->freightVerifyService->sendReleasedToCarrier($vin, $body, 1);
                break;

            case Stage::STAGE_ST8_CODE:
                $this->freightVerifyService->sendCompoundExit($vin, $body, 1);
                break;
        }
    }
}
