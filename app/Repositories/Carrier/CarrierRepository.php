<?php

namespace App\Repositories\Carrier;

use App\Helpers\QueryParamsHelper;
use App\Models\Carrier;
use App\Models\Route;
use App\Models\Vehicle;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class CarrierRepository extends BaseRepository implements CarrierRepositoryInterface
{
    public function __construct(Carrier $carrier)
    {
        parent::__construct($carrier);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        return $this->model->all();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        return DataTables::customizable($query)->response();
    }

    /**
     * @param int $routeTypeId
     * @return Collection
     */
    public function findAllByRouteTypeId(int $routeTypeId): Collection
    {
        return $this->model->query()
                    ->whereHas('routes', function (Builder $q) use ($routeTypeId) {
                        $q->where('route_type_id', "=", $routeTypeId);
                    })
                    ->get();
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function matchVins(array $params): Collection
    {
        //obtenemos los destinos de los vehiculos que se han pasado
        $array_vehicles = Vehicle::select('destination_code_id')->whereIn('vin', $params['vins'])->distinct()->get()->toArray();

        $array_destinys = array_column($array_vehicles, "destination_code_id");

        //obtenemos las rutas que tienen los mismos codigos de destino
        $array_routes = Route::whereIn('destination_code_id', $array_destinys)->get();
        $array_to_match = [];//inicializamos un array con los ids de los carrier con los ids de todos los destinos posibles
        foreach ($array_routes as $route){
            $array_to_match[$route->carrier_id] = [];
            foreach ($array_destinys as $destiny){
                $array_to_match[$route->carrier_id][$destiny] = false;
            }
        }

        //recorremos el array y comprobamos que el mismo carrier tenga rutas con todos los destinos
        foreach ($array_to_match as $carrier_id => $destinys) {
            foreach ($destinys as $destiny => $value) {
                $rutas_disponibles = $array_routes
                    ->where('carrier_id', $carrier_id)
                    ->where('destination_code_id', $destiny);
                if($rutas_disponibles->isNotEmpty()){
                    $array_to_match[$carrier_id][$destiny] = true;
                }
            }
        }

        // recorremos el array de nuevo para comprobar que carrier tienen todos los destinos a true
        $array_carrier_ids_valid = [];
        foreach ($array_to_match as $carrier_id => $destinys) {
            $all_valid = true;
            foreach ($destinys as $destiny => $value){
                if(!$value){
                    $all_valid = false;
                }
            }
            if($all_valid){
                $array_carrier_ids_valid[] = $carrier_id;
            }
        }

        //obtenemos los carriers que son validos
        return Carrier::whereIn('id', $array_carrier_ids_valid)->get();
    }
}
