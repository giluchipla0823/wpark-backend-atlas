<?php

namespace App\Services\Application\Load;

use App\Exceptions\owner\BadRequestException;
use App\Helpers\FordSt8ApiHelper;
use App\Http\Controllers\Api\v1\Load\LoadTransportST8Controller;
use App\Http\Requests\FORD\TransportST8Request;
use App\Http\Resources\Load\LoadDatatablesResource;
use App\Http\Resources\Load\LoadResource;
use App\Http\Resources\Load\LoadVehiclesDatatablesResource;
use App\Models\Load;
use App\Models\Vehicle;
use App\Repositories\Load\LoadRepositoryInterface;
use App\Services\External\FORD\TransportST8Service;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadService
{
    /**
     * @var LoadRepositoryInterface
     */
    private $repository;

    public function __construct(LoadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Load $load
     * @return void
     * @throws Exception
     */
    public function confirmLeft(Load $load): void
    {
        if($load->processed === 1){
            throw new BadRequestException("Ya se confirmó anteriormente la salida de la carga seleccionada.");
        }

        $vehicles = $load->vehicles->load(['holds' => function($q){
            $q->wherePivot('deleted_at', null);
        }]);

        $routes = $load->carrier->routes;

        if(count($vehicles) !== 8){
            throw new BadRequestException("El load seleccionado debe tener 8 vehículos asignados.");
        }

        $errors_vehicles = '';

        foreach ($vehicles as $vehicle){
            if($vehicle->holds->isNotEmpty()){
                $errors_vehicles .= "El vin $vehicle->vin tiene las retenciones " . $vehicle->holds->implode('name', ',');
            }
            if($routes->whereIn('destination_code_id',$vehicle->destination_code_id)->isEmpty()){
                if($errors_vehicles !== ''){
                    $errors_vehicles .= " y ";
                }else{
                    $errors_vehicles .= "El vin $vehicle->vin, ";
                }
                $errors_vehicles .= "el código de destino no cumple con la ruta del transportista seleccionado.";
            }
            if($errors_vehicles !== ''){
                throw new Exception(
                    $errors_vehicles,
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        try{
            $loadtransport = new LoadTransportST8Controller();
            $res = $loadtransport->__invoke($load);
            $response = json_decode($res);
            if ($res->getStatusCode() !== Response::HTTP_OK) {
                $errors = $response->getBody()->getContents();
                throw new Exception($errors, $res->getStatusCode());
            }
        }catch (Exception $e){
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->update(['processed' => true], $load->id);


        // TODO: Realizar llamada api FreightVerify - CompoundExit
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->repository->update($params, $id);
    }

    public function checkVehicles(array $params): array
    {
        return $this->repository->checkVehicles($params);
    }


    /**
     * @param array $params
     * @return Load
     */
    public function generate(array $params): Load
    {
        return $this->repository->generate($params);
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return LoadResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = LoadDatatablesResource::collection($results['data']);

        $results['data'] = $resource->collection;

        return collect($results);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatablesVehicles(Load $load): Collection
    {
        $results = $this->repository->datatablesVehicles($load);

        $resource = LoadVehiclesDatatablesResource::collection($results['data']);

        $results['data'] = $resource->collection;

        return collect($results);
    }

    /**
     * @param Load $load
     * @param Vehicle $vehicle
     * @return void
     */
    public function unlinkVehicle(Load $load, Vehicle $vehicle): void
    {
        $this->repository->unlinkVehicle($load, $vehicle);
    }
}
