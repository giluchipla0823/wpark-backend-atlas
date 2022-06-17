<?php

namespace App\Services\Application\Vehicle;

use App\Exceptions\owner\BadRequestException;
use App\Helpers\StringHelper;
use App\Models\Vehicle;
use Exception;
use App\Models\Stage;
use App\Models\Transport;
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

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        StageRepositoryInterface $stageRepository,
        DesignRepositoryInterface $designRepository,
        ColorRepositoryInterface $colorRepository,
        DestinationCodeRepositoryInterface $destinationCodeRepository,
        FreightVerifyService $freightVerifyService
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->stageRepository = $stageRepository;
        $this->designRepository = $designRepository;
        $this->colorRepository = $colorRepository;
        $this->destinationCodeRepository = $destinationCodeRepository;
        $this->freightVerifyService = $freightVerifyService;
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
                'Faltan datos para poder crear o actualizar el vehículo con el EOC especificado',
                array_merge($params, ['design_code' => $designCode, 'color_code' => $colorCode, 'missing_data' => $missingData]),
                $vehicle
            );

            throw new Exception(
                sprintf(
                    'No se ha encontrado información de %s con el EOC especificado.',
                    StringHelper::replaceLastOccurrence(",", " y", implode(", ", $missingData))
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        $params['design_id'] = $design->id;
        $params['color_id'] = $color->id;
        $params['destination_code_id'] = $destinationCode->id;

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
        try {
            $message = !$vehicle ? "Vehículo creado" : "Vehículo actualizado";

            if (!$vehicle) {

                // Comprobar si el eoc ya existe
                if ($this->vehicleRepository->findBy(['eoc' => $params['eoc']])) {
                    throw new BadRequestException('El eoc especificado ya se encuentra registrado.');
                }

                $vehicle = $this->vehicleRepository->create($params);
            } else {
                $this->vehicleRepository->update($params, $vehicle->id);
            }

            $this->saveLog($message, $params, $vehicle);
        } catch (Exception $exc) {
            $this->saveLog($exc->getMessage(), $params, $vehicle);

            throw new Exception($exc->getMessage(), $exc->getCode());
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
}
