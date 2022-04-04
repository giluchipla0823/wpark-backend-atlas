<?php

namespace App\Services\Vehicle;

use App\Models\Vehicle;
use App\Repositories\Color\ColorRepositoryInterface;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Repositories\Vehicle\StageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class VehicleStageService
{
    // TODO: Servicio para crear/actualizar vehículos desde la api de Ford
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
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        StageRepositoryInterface $stageRepository,
        DesignRepositoryInterface $designRepository,
        ColorRepositoryInterface $colorRepository,
        DestinationCodeRepositoryInterface $destinationCodeRepository,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->stageRepository = $stageRepository;
        $this->designRepository = $designRepository;
        $this->colorRepository = $colorRepository;
        $this->destinationCodeRepository = $destinationCodeRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param array $params
     * @return Vehicle
     */
    public function vehicleStage(array $params): Vehicle
    {
        // TODO: Confirmar que extrae las posiciones correctas
        // ¿Qué debe pasar si alguno de los códigos de las relaciones no existe en la db?
        // Extracción del vin_short desde el eoc
        $eoc = str_replace(' ', '',$params['eoc']);
        $params['vin_short'] = substr($eoc, 21, 7);
        dd($params);
        // Extracción del modelo desde el eoc
        $designCode = substr($eoc, 19, 2);
        $design = $this->designRepository->findBy('code', $designCode);
        $params['model_id'] = $design->id;

        // Extracción del color desde el eoc
        $colorCode = substr($eoc, 19, 2);
        $color = $this->colorRepository->findBy('code', $colorCode);
        $params['color_id'] = $color->id;

        // Extracción del destination_code desde el eoc
        $destinationCodeCode = substr($eoc, 19, 2);
        $destinationCode = $this->destinationCodeRepository->findBy('code', $destinationCodeCode);
        $params['destination_code_id'] = $destinationCode->id;

        // Extracción del country desde el eoc
        $countryCode = substr($eoc, 19, 2);
        $country = $this->countryRepository->findBy('code', $countryCode);
        $params['country_id'] = $country->id;
    }
}
