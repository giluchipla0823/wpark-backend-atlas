<?php

namespace App\Services\Application\Load;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Exceptions\owner\BadRequestException;
use App\Exceptions\owner\BaseOwnerException;
use App\Helpers\FordSt8ApiHelper;
use App\Http\Controllers\Api\v1\Load\LoadTransportST8Controller;
use App\Http\Requests\FORD\TransportST8Request;
use App\Http\Resources\Load\LoadDatatablesResource;
use App\Http\Resources\Load\LoadResource;
use App\Http\Resources\Load\LoadVehiclesDatatablesResource;
use App\Models\Dealer;
use App\Models\Load;
use App\Models\Parking;
use App\Models\Slot;
use App\Models\Stage;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\Load\LoadRepositoryInterface;
use App\Services\External\FORD\TransportST8Service;
use App\Services\External\FreightVerify\FreightVerifyService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoadService
{
    /**
     * @var LoadRepositoryInterface
     */
    private $repository;
    /**
     * @var FreightVerifyService
     */
    private $freightVerifyService;
    /**
     * @var LoadTransportST8Service
     */
    private $loadTransportST8Service;

    public function __construct(
        LoadRepositoryInterface $repository,
        FreightVerifyService $freightVerifyService,
        LoadTransportST8Service $loadTransportST8Service
    )
    {
        $this->repository = $repository;
        $this->freightVerifyService = $freightVerifyService;
        $this->loadTransportST8Service = $loadTransportST8Service;
    }

    /**
     * @param Load $load
     * @return void
     * @throws BadRequestException
     * @throws GuzzleException
     * @throws Exception
     */
    public function confirmLeft(Load $load): void
    {
        if($load->processed === 1){
            throw new BadRequestException("Ya se confirmó anteriormente la salida de la carga seleccionada.");
        }

        /* @var Collection $vehicles */
        $vehicles = $load->vehicles->load(['holds' => function($q){
            $q->wherePivot('deleted_at', null);
        }]);

        if(count($vehicles) === 0) {
            throw new BadRequestException("El load seleccionado debe tener vehículos asignados.");
        }

        $routes = $load->carrier->routes;

        $errors_vehicles = '';

        foreach ($vehicles as $vehicle){
            if ($vehicle->holds->isNotEmpty()) {
                $errors_vehicles .= "El vin $vehicle->vin tiene las retenciones " . $vehicle->holds->implode('name', ',');
            }

            if ($routes->whereIn('destination_code_id', $vehicle->destination_code_id)->isEmpty()) {
                if($errors_vehicles !== ''){
                    $errors_vehicles .= " y ";
                }else{
                    $errors_vehicles .= "El vin $vehicle->vin, ";
                }
                $errors_vehicles .= "el código de destino no cumple con la ruta del transportista seleccionado.";
            }

            if ($errors_vehicles !== '') {
                throw new BadRequestException($errors_vehicles);
            }
        }

        // TODO: A la espera de confirmación por SALVADOR BELTRAN
        // $this->loadTransportST8Service->process($load);

        DB::transaction(function () use ($load, $vehicles) {
            $this->update(['processed' => true], $load->id);

            // Limpiar parking, filas y slots.
            foreach ($vehicles as $vehicle) {
                $position = $vehicle->lastConfirmedMovement->destinationPosition;

                if (get_class($position) === Slot::class) {
                    $slot = $position;
                    $slot->release($vehicle->design->length);
                } else {
                    /* @var Parking $parking */
                    $parking = $position;
                    $parking->release();
                }

                $vehicle->stages()->sync([
                    Stage::STAGE_VEHICLE_LEFT_ID => [
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now()
                    ]
                ]);

                $vehicle->states()->sync([
                    State::STATE_LEFT_ID => [
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now()
                    ]
                ]);

                $this->freightVerifyService->sendCompoundExit($vehicle->vin, [
                    "assetId" => $load->carrier->code,
                    "equipmentNumber" => $load->license_plate,
                ], 1);
            }
        });
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

    /**
     * @param array $params
     * @return array
     */
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

    /**
     * @param Request $request
     * @return Collection
     */
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
     * @param Load $load
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

    /**
     * @param Load $load
     * @return array
     * @throws BadRequestException
     */
    public function downloadAlbaran(Load $load): array
    {
        /* @var Collection $vehicles */
        $vehicles = $load->vehicles;

        if (count($vehicles) === 0) {
            throw new BadRequestException("El load seleccionado no tiene asignado vehículos");
        }

        $dealer = Dealer::find(Dealer::UNKNOWN_ID);

        $vehicles = $vehicles
            ->map(function($vehicle) use ($dealer) {
                $lastConfirmedMovement = $vehicle->lastConfirmedMovement;

                if (get_class($lastConfirmedMovement->destinationPosition) === Slot::class) {
                    $slot = $lastConfirmedMovement->destinationPosition;

                    $vehicle->additional_data = (object) [
                        'position' => (object) [
                            'slot' => $slot,
                            'row' =>  $slot->row,
                        ]
                    ];
                }

                if (!$vehicle->dealer) {
                    $vehicle->dealer = $dealer;
                }

                $dealerFullAddress = $dealer->name;

                if ($vehicle->dealer->id !== Dealer::UNKNOWN_ID) {
                    $dealerFullAddress = "{$vehicle->dealer->name}<br>{$vehicle->dealer->street}<br>{$vehicle->dealer->zip_code} {$vehicle->dealer->city}";
                }

                $vehicle->dealer->full_address = $dealerFullAddress;

                return $vehicle;
            })
            ->sortBy('additional_data.position.slot.slot_number');

        $totalWeight = array_sum($vehicles->pluck('design.weight')->toArray());

        return [
            'load' => $load,
            'vehicles' => $vehicles,
            'counter_vehicles' => count($vehicles),
            'total_weight' => $totalWeight
        ];
    }
}
