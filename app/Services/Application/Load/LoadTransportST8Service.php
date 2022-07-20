<?php

namespace App\Services\Application\Load;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Exceptions\owner\BadRequestException;
use App\Exceptions\owner\TransportST8Exception;
use App\Helpers\FordSt8ApiHelper;
use App\Models\Load;
use App\Services\External\FORD\TransportST8Service;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class LoadTransportST8Service
{

    /**
     * @var TransportST8Service
     */
    private $transportST8Service;

    public function __construct(
        TransportST8Service $transportST8Service
    )
    {
        $this->transportST8Service = $transportST8Service;
    }

    /**
     * @param Load $load
     * @return array
     * @throws BadRequestException
     * @throws FordStandardErrorException
     * @throws GuzzleException
     * @throws TransportST8Exception
     */
    public function process(Load $load): array
    {
        if ($load->ready === 0) {
            throw new BadRequestException("La carga aún no está lista para ser procesada.");
        }

        if ($load->processed === 1) {
            throw new BadRequestException("La carga ya ha sido procesada anteriormente.");
        }

        /* @var Collection $vehicles */
        $vehicles = $load->vehicles;

        if ($vehicles->count() === 0) {
            throw new BadRequestException("El load no tiene vehículos asignados.");
        }

        $transportType = FordSt8ApiHelper::getTransportType($load->exit_transport_id);

        $transportContent = [];

        $vehiclesGroupByCdmCode = $vehicles->map(function($vehicle) {
            return [
                "cdmCode" => $vehicle->route->cdm_code,
                "vin" => $vehicle->vin,
                "imported" => (boolean) $vehicle->design->manufacturing
            ];
        })
            ->groupBy('cdmCode')
            ->toArray();

        foreach ($vehiclesGroupByCdmCode as $cdmCode => $vehicleItems) {
            $transportContent[] = [
                "cdmCode" => $cdmCode,
                "vehicles" => array_map(function ($vehicle) {
                    unset($vehicle['cdmCode']);
                    return $vehicle;
                }, $vehicleItems)
            ];
        }

        try {
            return $this->transportST8Service->connection([
                "id" => $load->transport_identifier,
                "type" => $transportType,
                "transportContent" => $transportContent
            ]);
        } catch (FordStandardErrorException $exc) {
            throw new TransportST8Exception('Error en API ST8', $exc->getStatusCode(), $exc);
        }
    }
}
