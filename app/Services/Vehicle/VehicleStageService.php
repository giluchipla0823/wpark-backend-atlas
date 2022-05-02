<?php

namespace App\Services\Vehicle;

use App\Repositories\Color\ColorRepositoryInterface;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Repositories\Vehicle\StageRepositoryInterface;

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
        // Añadir vin del vehículo
        $params['vin'] = $params['pvin'];

        // Extraer y añadir vin_short desde el eoc
        $params['vin_short'] = substr($params['eoc'], 24, 7);

        // Extraer y añadir modelo desde el eoc
        $designCode = substr($params['eoc'], 22, 2);
        $design = $this->designRepository->findByCode($designCode);

        // Extraer y añadir color desde el eoc
        $colorCode = substr($params['eoc'], 22, 2).$params['eoc'][72];
        $color = $this->colorRepository->findByCode($colorCode);

        // Añadir código de destino
        // $destination_code = substr($params['eoc'], 9, 2);
        // Se puede sacar el código de destino por el eoc pero lo pasan a parte porque puede no estar actualizado en el eoc
        $destinationCode = $this->destinationCodeRepository->findByCode(trim($params['destination']));

        // Añadir método de entrada (Se añade por defecto 1 correspondiente a la factoria)
        $params['entry_transport_id'] = 1;

        // Comprobación si existe o no el stage
        $stage = $this->stageRepository->findByCode($params['station']);

        // Comprobación si existe o no el vehículo para crear o actualizar
        $vehicle = $this->vehicleRepository->findByVin($params['pvin']);

        if($design != null && $color != null && $destinationCode != null && $stage != null){
            $params['design_id'] = $design->id;
            $params['color_id'] = $color->id;
            $params['destination_code_id'] = $destinationCode->id;

            if ($vehicle == null) {
                $this->vehicleRepository->create($params);
            }else{
                $this->vehicleRepository->update($params, $vehicle->id);
            }
        }else{
            // TODO: Sacar errores en excel para importarlos (FASE 2)
            dd("No existe alguna información en la base de datos");
        }


    }
}
