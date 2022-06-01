<?php

namespace App\Repositories\Load;

use App\Http\Resources\Route\RouteResource;
use App\Models\Carrier;
use App\Models\Load;
use App\Models\RouteType;
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

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function checkVehicles(array $params): array
    {
        $vins = array_unique($params['vins']);
        $carrier_id = $params['carrier_id'];
        $array_check = [];

        /* @var Collection $vehicles */
        $vehicles = Vehicle::whereIn('vin', $vins)->get();
        $carrier = Carrier::with('routes')->find($carrier_id);

        if ($carrier->routes->isEmpty()) {
            throw new Exception(
                "El transportista seleccionado no tiene rutas definidas.",
                Response::HTTP_BAD_REQUEST
            );
        }

        foreach ($vins as $vinValue) {
            $vehicle = $vehicles->where("vin", "=", $vinValue)->first();
            $existsVin = $vehicle instanceof Vehicle;
            $destinationCode = null;

            if ($existsVin) {
                $destinationCodeRelationship = $vehicle->destinationCode;

                $destinationCode = $destinationCodeRelationship ? [
                    "id" => $destinationCodeRelationship->id,
                    "code" => $destinationCodeRelationship->code
                ] : null;
            }

            $array_check[$vinValue] = [
                "vehicle" => [
                    "id" => $existsVin ? $vehicle->id : null,
                    "vin" => $vinValue,
                    "destination_code" => $destinationCode
                ],
                "exists" => $existsVin,
                "enable_to_load" => $existsVin
            ];

            if (!$existsVin) {
                continue;
            }

            $errors = [];

            /**
             * Verificamos si el vehículo tiene Holds(Retenciones).
             */
            if ($vehicle->holds->isNotEmpty()) {
                $hold_errors = [];

                foreach ($vehicle->holds as $hold) {
                    $hold_errors[] = [
                        "id" => $hold->id,
                        "name" => $hold->name
                    ];
                }

                $errors[] = ["condition" => "hold", "items" => $hold_errors];
                $array_check[$vehicle->vin]['enable_to_load'] = false;
            }

            /**
             * De todas las rutas que tiene el transportista seleccionado, comprobamos que coincida al menos una
             * con el código de destino del vehículo
             */
            $match = false;

            foreach ($carrier->routes as $route) {
                if ($vehicle->destination_code_id === $route->destination_code_id) {
                    $match = true;
                }
            }

            if (!$match) {

                /**
                 * Si no coinciden los códigos de destino del vehículo con el transportista seleccionado,
                 * entonces procedemos a obtener los transportistas que pueden llevar al vehículo.
                 *
                 */
                $carriers = Route::where('destination_code_id', "=", $vehicle->destination_code_id)
                                ->with('carrier')
                                ->get()
                                ->pluck('carrier')
                                ->map(function(Carrier $carrier) {
                                   return [
                                     "id" => $carrier->id,
                                     "name" => $carrier->name,
                                     "short_name" => $carrier->short_name,
                                     "code" => $carrier->code,
                                   ];
                                });

                $errors[] = [
                    "condition" => "carrier",
                    "items" => $carriers
                ];;

                $array_check[$vehicle->vin]['enable_to_load'] = false;
            }

            if (!empty($errors)) {
                $array_check[$vehicle->vin]['errors'] = $errors;
            } else {
                $routes = $carrier->routes
                    ->filter(function(Route $route) use ($vehicle) {
                        return $route->destination_code_id === $vehicle->destination_code_id;
                    })
                    ->sortBy('route_type_id')
                    ->values();

                $array_check[$vehicle->vin]['routing_codes'] = RouteResource::collection($routes)->collection;
            }
        }

        return array_values($array_check);
    }
}
