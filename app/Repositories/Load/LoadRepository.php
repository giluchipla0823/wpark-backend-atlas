<?php

namespace App\Repositories\Load;

use App\Exceptions\owner\BadRequestException;
use App\Exceptions\owner\NotFoundException;
use App\Http\Resources\Route\RouteResource;
use App\Helpers\QueryParamsHelper;
use App\Models\Carrier;
use App\Models\Load;
use App\Models\Notification;
use App\Models\RouteType;
use App\Models\Row;
use App\Models\Slot;
use App\Models\Stage;
use App\Models\State;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

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
        $transport_identifier = Str::random("10");
        $license_plate = $params['license_plate'];
        $carrier = Carrier::with('routes')->find($carrier_id);

        $vins = array_column($params["vehicles"], "vin");

        $checkVins = [];

        $vehicles = Vehicle::with(['holds' => function ($q) {
            $q->wherePivot('deleted_at', null);
        }])->whereIn('vin', $vins)->get();

        $vehicles->map(function($vehicle) {
            return $vehicle;
        });

        $zonesValidForLoads = Zone::whereIn('id', Zone::getValidZonesForLoads())->pluck('name')->toArray();

        // Comprobamos que no existan vin duplicados y todos los vin existan
        foreach ($vins as $key => $vin) {
            $index = ($key + 1);

            if (in_array($vin, $checkVins)) {
                throw new BadRequestException(sprintf("El veh??culo N??%s con [%s] se encuentra duplicado.", $index, $vin));
            }

            if ($vehicles->where('vin', $vin)->isEmpty()) {
                throw new NotFoundException(sprintf("El veh??culo N??%s con vin [%s] no existe.", $index, $vin));
            }
        }

        foreach ($vehicles as $key => $vehicle) {
            $index = ($key + 1);

            $position = $vehicle->lastConfirmedMovement->destinationPosition;

            $parking = get_class($position) === Slot::class ? $position->row->parking : $position;

            $zone = $parking->area->zone;

            // Comprobar que el veh??culo se encuentra posicionado en un parking que NO sea de zona PLANTA.
            if (!$zone->checkIsValidForLoad()) {
                throw new BadRequestException(sprintf(
                    "El veh??culo N??%s con [%s] no puede ser asignado a una carga porque se encuentra posicionado en zona %s. Las zonas v??lidas para cargas son: %s",
                    $index,
                    $vehicle->vin,
                    $zone,
                    implode(", ", $zonesValidForLoads)
                ));
            }

            // Comprobamos que los vehiculos no tengan ya un load
            if ($vehicle->loads) {
                $load = $vehicle->loads;

                throw new BadRequestException(sprintf(
                    "El veh??culo N??%s con [%s] se encuentra asignado en la carga: [%s].",
                    $index,
                    $vehicle->vin,
                    $load->transport_identifier
                ));
            }

            // Comprobamos que los vehiculos no tengan bloqueos
            if ($vehicle->holds->isNotEmpty()) {
                $holds = $vehicle->holds->implode('name', ',');

                throw new BadRequestException(sprintf(
                    "El veh??culo N??%s con [%s] tiene asignado las retenciones: [%s].",
                    $index,
                    $vehicle->vin,
                    $holds
                ));
            }

            // Comprobamos que el destino del vehiculo y el del transportista coinciden
            if ($carrier->routes->where('destination_code_id', $vehicle->destination_code_id)->isEmpty()) {
                throw new BadRequestException(sprintf(
                    "El veh??culo N?? %s con vin %s no tiene el mismo c??digo de destino del transportista [%s].",
                    $index,
                    $vehicle->vin,
                    $carrier->name
                ));
            }
        }

        // Obtener la fila a la que pertenecen los veh??culos
        $positions = $vehicles
                        ->pluck('lastConfirmedMovement.destinationPosition')
                        ->map(function ($position) {
                            $position->type = get_class($position);

                            return $position;
                        });

        $positionsTypes = $positions->pluck('type')->unique();

        $category = Load::MULTIPLES_CATEGORY;

        if (
            $positionsTypes->count() === 1 &&
            $positionsTypes->first() === Slot::class &&
            $positions->pluck('row.id')->unique()->count() === 1
        ) {
            $row = $positions->first()->row;
            $category = $row->category;

            $notification = Notification::where([
                ['resourceable_type', '=' , Row::class],
                ['resourceable_id', '=' , $row->id],
            ])->first();

            if ($notification) {
                $notification->reat_at = Carbon::now();
                $notification->save();
            }
        }

        $user = Auth::user();
        $compound = $user->currentAccessToken()->compound;

        DB::beginTransaction();

        try {

            // Creamos el load
            $load = $this->model->create([
                'transport_identifier' => $transport_identifier,
                'license_plate' => $license_plate,
                'trailer_license_plate' => $params['trailer_license_plate'] ?? null,
                'carrier_id' => $carrier_id,
                'exit_transport_id' => $transport_exit_id,
                'compound_id' => $compound->id,
                'ready' => 1,
                'category' => $category
            ]);

            foreach ($params["vehicles"] as $item) {
                $vin = $item['vin'];

                Vehicle::where('vin', $vin)->update([
                    'load_id' => $load->id,
                    'route_id' => $item['route_id']
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $load;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        return $query->get();
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $table = $this->model->getTable();
        $query = $this->model->query()
            ->select(["{$table}.*"])
            ->with(QueryParamsHelper::getIncludesParamFromRequest());

        return Datatables::customizable($query)->response();
    }

    /**
     * @param Load $load
     * @return array
     */
    public function datatablesVehicles(Load $load): array
    {
        $query = $load->vehicles();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        return Datatables::customizable($query)->response();
    }

    public function unlinkVehicle(Load $load, Vehicle $vehicle):void
    {
        if ($load->processed === 1) {
            throw new Exception(
                "No se puede realizar esta acci??n, porque el load ya se proces?? anteriormente.",
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($load->ready === 1) {
            throw new Exception(
                "No se puede realizar esta acci??n, porque el load se encuentra listo para ser transportado.",
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($load->id !== $vehicle->load_id) {
            throw new Exception(
                "El veh??culo no se encuentra en el load especificado",
                Response::HTTP_BAD_REQUEST
            );
        }

        $vehicle->load_id = null;
        $vehicle->save();
    }

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
             * Verificamos si el veh??culo ya se encuentra en un "Load". Si el veh??culo est?? en un load
             */
            if ($vehicle->loads) {
                $errors[] = [
                    "condition" => "load",
                    "items" => [
                        [
                            "id" => $vehicle->loads->id,
                            "transport_identifier" => $vehicle->loads->transport_identifier,
                        ]
                    ]
                ];
                $array_check[$vehicle->vin]['enable_to_load'] = false;
                $array_check[$vehicle->vin]['errors'] = $errors;
                continue;
            }

            /**
             * Verificamos si el veh??culo tiene Holds(Retenciones).
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
             * con el c??digo de destino del veh??culo
             */
            $match = false;

            foreach ($carrier->routes as $route) {
                if ($vehicle->destination_code_id === $route->destination_code_id) {
                    $match = true;
                }
            }

            if (!$match) {

                /**
                 * Si no coinciden los c??digos de destino del veh??culo con el transportista seleccionado,
                 * entonces procedemos a obtener los transportistas que pueden llevar al veh??culo.
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
