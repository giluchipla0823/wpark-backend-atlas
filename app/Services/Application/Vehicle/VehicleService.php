<?php

namespace App\Services\Application\Vehicle;

use App\Helpers\Paginator\EloquentPaginator;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Row\RowVehicleResource;
use App\Http\Resources\Vehicle\InfoVehicleResource;
use App\Http\Resources\Vehicle\VehicleDatatableResource;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Http\Resources\Vehicle\VehicleShowResource;
use App\Http\Resources\Vehicle\VehicleStateResource;
use App\Models\Row;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VehicleService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository
    )
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $results = $this->vehicleRepository->all($request);

        if ($results instanceof LengthAwarePaginator) {
            return (new EloquentPaginator($results, VehicleResource::class))->collection();
        }

        return VehicleResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->vehicleRepository->datatables($request);

        $collection = VehicleDatatableResource::collection(
            $results->get('data')
        )->collection;

        $results->put('data', $collection);

        return $results;
    }

    /**
     * @param Vehicle $vehicle
     * @return VehicleShowResource
     */
    public function show(Vehicle $vehicle): VehicleShowResource
    {
        return new VehicleShowResource($vehicle);
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->vehicleRepository->update($params, $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->vehicleRepository->delete($id);
    }

    public function restore(int $id): void
    {
        $this->vehicleRepository->restore($id);
    }

    /**
     * @param Row $row
     * @return Collection
     */
    public function findAllByRow(Row $row): Collection
    {
        $results = $this->vehicleRepository->findAllByRow($row);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = RowVehicleResource::collection($data);

            $results->put('data', $resource->collection->toArray());

            return $results;
        }

        return RowVehicleResource::collection($results)->collection;
    }

    /**
     * @param State $state
     * @return Collection
     */
    public function findAllByState(State $state): Collection
    {
        $results = $this->vehicleRepository->findAllByState($state);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = VehicleStateResource::collection($data);

            $results->put('data', $resource->collection->toArray());

            return $results;
        }

        return VehicleStateResource::collection($results)->collection;
    }

    /**
     * @param string $vin
     * @return VehicleShowResource
     * @throws Exception
     */
    public function searchByVin(string $vin): VehicleShowResource
    {
        if (strlen($vin) > Vehicle::VIN_SHORT_MAX_LENGTH) {
            $vehicle = $this->vehicleRepository->findBy(['vin' => $vin]);
        } else {
            $vehicle = $this->vehicleRepository->findBy(['vin_short' => $vin]);
        }

        if (!$vehicle) {
            throw new Exception("No se encontró información del vehículo", Response::HTTP_NOT_FOUND);
        }

        // El vehículo pasa a state "ON TERMINAL" cuando el operario interactua por primera vez con el vehículo.
        if (!$vehicle->states->where('id', State::STATE_ON_TERMINAL_ID)->first()) {
            $vehicle->states()->sync([
                State::STATE_ON_TERMINAL_ID => [
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ]
            ], false);
        }

        return new VehicleShowResource($vehicle);
    }


    public function massiveChangeData(array $params)
    {
        $vins = $params['vins'];
        $option = $params['option_change_data'];
        $destinationCodeId = $params['destination_code'];
        $info = $params['info'];

        $vehicles = Vehicle::whereIn('vin', $vins)->get();

        foreach ($vehicles as $vehicle) {

            DB::beginTransaction();

            try {
                if ($option === 1) { // destination code
                    /* @var VehicleMovementsService $vehicleMovementService */
                    $vehicleMovementService = app()->make(VehicleMovementsService::class);

                    $vehicle->destination_code_id = $destinationCodeId;
                    $vehicle->save();

                    $vehicleMovementService->vehicleMatchRules($vehicle);
                } else { // info

                    $vehicle->info = $info;
                    $vehicle->save();
                }

                DB::commit();
            } catch (Exception $exc) {
                DB::rollBack();
            }
        }
    }
}
