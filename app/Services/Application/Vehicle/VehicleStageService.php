<?php

namespace App\Services\Application\Vehicle;

use App\Models\Transport;
use App\Repositories\Color\ColorRepositoryInterface;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;
use App\Repositories\Vehicle\StageRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        StageRepositoryInterface $stageRepository,
        DesignRepositoryInterface $designRepository,
        ColorRepositoryInterface $colorRepository,
        DestinationCodeRepositoryInterface $destinationCodeRepository,
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->stageRepository = $stageRepository;
        $this->designRepository = $designRepository;
        $this->colorRepository = $colorRepository;
        $this->destinationCodeRepository = $destinationCodeRepository;
    }

    /**
     * @param array $params
     * @return void
     */
    public function vehicleStage(array $params): void
    {
        // Log de petición del servicio
        activity()
            ->withProperties([
                'tracking-date' => $params['tracking-date'],
                'lvin' => $params['lvin'],
                'pvin' => $params['pvin'],
                'station' => $params['station'],
                'eoc' => $params['eoc'],
                'manual' => $params['manual'],
                'destination' => $params['destination']
            ])
            ->event('Datos recibidos')
            ->log('Tracking-point');

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

        // Comprobación si existe o no el vehículo para crear o actualizar
        $vehicle = $this->vehicleRepository->findBy(['vin' => $params['pvin']]);

        if ($design != null && $color != null && $destinationCode != null && $stage != null) {
            $params['design_id'] = $design->id;
            $params['color_id'] = $color->id;
            $params['destination_code_id'] = $destinationCode->id;

            if ($vehicle == null) {
                $this->vehicleRepository->create($params);
            } else {
                $this->vehicleRepository->update($params, $vehicle->id);
            }
        } else {
            // TODO: Sacar errores en excel para importarlos (FASE 2)
            activity()
                    ->withProperties([
                        'lvin' => $params['lvin'],
                        'pvin' => $params['pvin'],
                        'station' => $params['station'],
                        'eoc' => $params['eoc'],
                        'vin_short' => $params['vin_short'],
                        'design_code' => $designCode,
                        'color_code' => $colorCode,
                        'destination_code' => $params['destination'],
                        'entry_transport_id' => $params['entry_transport_id'],
                        'relations' => [
                            'manual' => $params['manual'],
                            'tracking_date' => $params['tracking-date']
                        ]
                    ])
                    ->event('Faltan datos para poder crear o actualizar el vehículo')
                    ->log('Tracking-point');
            throw new Exception(
                "No se ha podido crear o actualizar el vehículo.",
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
