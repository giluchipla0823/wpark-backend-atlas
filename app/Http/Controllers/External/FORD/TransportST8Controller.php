<?php

namespace App\Http\Controllers\External\FORD;

use App\Http\Controllers\ApiController;
use App\Http\Requests\FORD\TransportST8Request;
use App\Models\Load;
use App\Models\Route;
use App\Models\Vehicle;
use App\Services\External\FORD\TransportST8Service;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\FordSt8ApiHelper;

class TransportST8Controller extends ApiController
{
    /**
     * Provision a new web server.
     *
     * @param TransportST8Request $request
     * @return void
     */
    public function __invoke(TransportST8Request $request): JsonResponse
    {
        $transport_id = $request->id;
        $transport_type = $request->type;
        $transport_content = $request->transportContent;
        $vins = [];
        $cdmCodes = [];
        //sacamos los codigos de las rutas y los vins de los vehiculos
        foreach ($transport_content as $item) {
            $cdmCodes[] = $item['cdmCode'];
            foreach ($item['vehicles'] as $vehicle){
                $vins[] = $vehicle['vin'];
            }
        }

        //sacamos el Load con el id de transporte que se ha enviado
        $load = Load::where('transport_identifier', $transport_id)->first();
        //Montamos la estructura del mensaje de error
        $error_data = FordSt8ApiHelper::getStandardErrorFormat();
        $errors_messages = [];//array para almacenar los distintos errores

        if(is_null($load)){//no hay load con ese id de transporte
            $errors_messages[] = "Load with transport_identifier: $transport_id doesn't not exists";
        }
        if(!in_array($transport_type, ['Truck','Rail'])){//sino es ninguno de estos tipos
            $errors_messages[] = "Type transport: $transport_type doesn't not exists";
        }

        $vin_exists = Vehicle::whereIn('vin', $vins)->get();//sacmos los vehiculos con los vins
        foreach ($vins as $vin) {
            if($vin_exists->where('vin', $vin)->isEmpty()){//sino existe el vin en la BD
                $errors_messages[] = "Vehicle with vin '$vin' doesn't not exists";
            }
        }

        $routes_exists = Route::whereIn('cdm_code', $cdmCodes)->get();
        foreach ($cdmCodes as $cdm_code) {
            if($routes_exists->where('cdm_code', $cdm_code)->isEmpty()){//sino existe el vin en la BD
                $errors_messages[] = "Route with cdm_code '$cdm_code' doesn't not exists";
            }
        }

        if(!empty($errors_messages)){
            $error_data['error']['messages'] = $errors_messages;
            return $this->makeResponse($error_data, 'NOT FOUND', Response::HTTP_NOT_FOUND);
        }

        //Montamos los parametros para hacer la llamada a la API
        $type = 'Truck';
        if($load->exit_transport_id == 2){
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
        $ws_ts8->connection($params);

        return $this->showMessage(null, Response::HTTP_NO_CONTENT);

    }
}
