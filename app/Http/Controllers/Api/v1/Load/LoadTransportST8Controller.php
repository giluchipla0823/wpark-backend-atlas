<?php

namespace App\Http\Controllers\Api\v1\Load;

use Exception;
use App\Models\Load;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\Application\Load\LoadTransportST8Service;

class LoadTransportST8Controller extends ApiController
{
    /**
     * @var LoadTransportST8Service
     */
    private $loadTransportST8Service;

    public function __construct(
        LoadTransportST8Service $loadTransportST8Service
    )
    {
        $this->loadTransportST8Service = $loadTransportST8Service;
    }

    /**
     * Provision a new web server.
     *
     * "id": "String (xChar)", // Tabla "loads" y columna "transport_identifier"
     * "type": "[Truck|Rail]", // Tabla "loads" y columna "exit_transport_id". Si el valor es 2 -> "Rail", Si el valor es 3 -> Truck
     * "transportContent": [
     *     {
     *     "cdmCode": "String (3char)", // Como se conoce el transportista de la carga y el transportista está relacionado con la tabla "routes", entonces este campo es "cdm_code"
     *       "vehicles": [
     *       "vin": "String (17char)", // Vin de la tabla "vehicles"
     *       "imported": "Boolean" // Columna "manufacturing" de la tabla "designs". Al estar relacionado "vehicles" con "designs", pues, el acceso a esta columna se tiene.
     *        ]
     *     }
     * ]
     *
     * @param Load $load
     * @return JsonResponse
     * @throws Exception
     * @throws GuzzleException
     */
    public function __invoke(Load $load): JsonResponse
    {
        $response = $this->loadTransportST8Service->process($load);

        return $this->successResponse($response);
    }
}
