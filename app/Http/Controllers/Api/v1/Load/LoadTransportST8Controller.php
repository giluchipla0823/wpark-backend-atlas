<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Services\External\FORD\TransportST8Service;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Transport;

class LoadTransportST8Controller extends Controller
{
    /**
     * Provision a new web server.
     *
     *         
     * "id": "String (xChar)", // Tabla "loads" y columna "transport_identifier"
	 * "type": "[Truck|Rail]", // Tabla "loads" y columna "exit_transport_id". Si el valor es 2 -> "Rail", Si el valor es 3 -> Truck
	 * "transportContent": [
	 *	 {
     *     "cdmCode": "String (3char)", // Como se conoce el transportista de la carga y el transportista está relacionado con la tabla "routes", entonces este campo es "cdm_code"
	 *	   "vehicles": [
     *       "vin": "String (17char)", // Vin de la tabla "vehicles"
     *       "imported": "Boolean" // Columna "manufacturing" de la tabla "designs". Al estar relacionado "vehicles" con "designs", pues, el acceso a esta columna se tiene.
	 *	    ]
	 *	 }
	 * ]
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Load $load)
    {
        if($load->ready === 0){
            throw new Exception(
                "La carga aún no está lista para ser procesada.",
                Response::HTTP_BAD_REQUEST
            );
        }

        if($load->processed === 1){
            throw new Exception(
                "La carga ya ha sido procesada anteriormente.",
                Response::HTTP_BAD_REQUEST
            );
        }


        $type = 'Truck';
        if($load->exit_transport_id == Transport::TRANSPORT_TRAIN_ID){
            $type = 'Rail';
        }
        $routes = $load->carrier->routes;

        $array_routes = [];
        foreach($routes as $route){
            $array_routes[$route->cdm_code] = $route->destination_code_id;
        }
        $array_vehicles = [];
        foreach ($load->vehicles as $vehicle){
            foreach ($array_routes as $cdm_code => $destination_code_id){
                if($vehicle->destination_code_id == $destination_code_id){
                    $array_vehicles[] = [
                        "cdmCode" => $cdm_code,
			            "vehicles" => [
                            "vin" => $vehicle->vin,
                            "imported" => (boolean)$vehicle->design->manufacturing
			            ]

                    ];
                }
            }
        }

        $params = [
            "id" => $load->transport_identifier,
            "type" => $type,
            "transportContent" => $array_vehicles
        ];
        $ws_ts8 = new TransportST8Service();
        $res = $ws_ts8->connection($params);

        return $this->successResponse($res, 'Transport ST8 realizado correctamente.', Response::HTTP_CREATED);
    }
}
