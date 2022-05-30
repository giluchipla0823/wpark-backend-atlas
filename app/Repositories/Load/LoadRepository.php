<?php

namespace App\Repositories\Load;

use App\Models\Carrier;
use App\Models\Load;
use App\Models\Vehicle;
use App\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LoadRepository extends BaseRepository implements LoadRepositoryInterface
{
    public function __construct(Load $load)
    {
        parent::__construct($load);
    }

    /**
     * @param array $params
     * @return Load
     * @throws Exception
     */
    public function generate(array $params): Load
    {
        $carrier_id = $params['carrier_id'];
        $transport_exit_id = $params['transport_exit_id'];
        $transport_identifier = $params['transport_identifier'];
        $vins = $params['vins'];
        $license_plate = $params['license_plate'];
        $compound_id = $params['compound_id'];
        $carrier = Carrier::with('routes')->find($carrier_id);
        $vehicles = Vehicle::with(['holds' => function ($q) {
            $q->wherePivot('deleted_at', null);
        }])->whereIn('vin', $vins)->get();

        //comprobamos que todos los vin existan
        foreach ($vins as $vin) {
            if ($vehicles->where('vin', $vin)->isEmpty()) {
                throw new Exception(
                    "El vin [$vin] especificado no existe",
                    Response::HTTP_NOT_FOUND
                );
            }
        }
        //cmprobamos que los vehiculos no tengan bloqueos
        foreach ($vehicles as $vehicle) {
            if ($vehicle->holds->isNotEmpty()) {
                $holds = $vehicle->holds->implode('name', ',');
                throw new Exception(
                    "El vin [$vehicle->vin] tiene asignado las retenciones: [$holds]",
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        //comprobamos que el destino del vehiculo y el del transportista coinciden
        foreach ($vehicles as $vehicle) {
            if ($carrier->routes->where('destination_code_id', $vehicle->destination_code_id)->isEmpty()) {
                throw new Exception(
                    "El vin [$vehicle->vin] no tiene el mismo código de destino del transportista [$carrier->name].",
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        DB::beginTransaction();

        try {
            //creamos el load
            $load = new Load([
                'transport_identifier' => $transport_identifier,
                'license_plate' => $license_plate,
                'trailer_license_plate' => null,
                'carrier_id' => $carrier_id,
                'exit_transport_id' => $transport_exit_id,
                'compound_id' => $compound_id,
                'ready' => 1
            ]);

            if ($load->save()) {
                $ids_vehicles = $vehicles->pluck('id')->toArray();
                Vehicle::whereIn('id', $ids_vehicles)->update(['load_id' => $load->id]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
        return $load;
    }

    /*
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        return $query->get();
    }

    public function checkVehicles(array $params): array
    {
        $vins = $params['vins'];
        $carrier_id = $params['carrier_id'];
        $array_check = [];
        $vehicles = Vehicle::whereIn('vin',$vins)->get();//obtenemos los vehiculos por el vin
        $carrier = Carrier::with('routes')->find($carrier_id);//obtenemos el transportista
        if ($carrier->routes->isEmpty()) {
            throw new Exception(
                "El transportista seleccionado no tiene rutas definidas.",
                Response::HTTP_BAD_REQUEST
            );
        }
        //recorremos los vehiculos para hacer las comprobaciones
        $array_need_carriers = [];
        foreach ($vehicles as $vehicle) {
            //iniciamos el array para cada vin
            $array_check[$vehicle->vin] = [
                "exists" => true,
                "enable_to_load" => true
            ];

            $errors = [];//iniciamos el array de errores

            if ($vehicle->holds->isNotEmpty()) {//comprobamos que el vehiculo no tenga ningun bloqueo
                $hold_errors = [];
                foreach ($vehicle->holds as $hold) {
                    $hold_errors[] = [//si tiene bloqueos los añadimos a un array
                        "id" => $hold->id,
                        "name" => $carrier->name
                    ];
                }
                $errors[] = ["condition" => "hold", "items" => $hold_errors];
                $array_check[$vehicle->vin]['enable_to_load'] = false;//marcamos como falso por tener bloqueos
            }
            //comprobamos que los destinos sean los mismos
            $destination_errors = [];
            $match = false;
            foreach ($carrier->routes as $route) {
                if ($vehicle->destination_code_id == $route->destination_code_id) {
                    $match = true;
                }
            }
            if (!$match) {
                $destination_errors[] = [//si los codigos de destino no coinciden
                    "condition" => "carrier",
                    "message" => "No cumple con el transportista seleccionado"
                ];
                array_push($errors, $destination_errors);
                $array_need_carriers[$vehicle->vin] = $vehicle->destination_code_id;
            }

            if (!empty($errors)) {
                $array_check[$vehicle->vin]['errors'] = $errors;
            }

        }


        //Obtenemos los transportistas que pueden llevar al vehiculo al destino
        if (!empty($array_need_carriers)) {
            $routes = Route::whereIn('destination_code_id', $array_need_carriers)->with('carrier')->get();
            foreach ($array_need_carriers as $vin => $destiny) {
                $valid_routes = $routes->where('destination_code_id', $destiny);
                foreach ($valid_routes as $valid_route) {
                    $array_check[$vin]['carriers_valid'][] = [
                        "id" => $valid_route->carrier->id,
                        "name" => $valid_route->carrier->name
                    ];
                }
            }
        }

        return $array_check;
    }
}
