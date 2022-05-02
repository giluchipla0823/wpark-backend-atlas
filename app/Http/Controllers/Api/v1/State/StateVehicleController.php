<?php

namespace App\Http\Controllers\Api\v1\State;

use App\Http\Controllers\ApiController;
use App\Models\State;
use App\Services\Vehicle\VehicleService;
use Illuminate\Http\JsonResponse;

class StateVehicleController extends ApiController
{

    /**
     * @var VehicleService
     */
    private $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->middleware('role:Super-Admin|admin');
        $this->vehicleService = $vehicleService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/state/{id}/vehicles",
     *      tags={"States"},
     *      summary="Vehicles List of state",
     *      description="Vehicles List of state",
     *      security={{"sanctum": {}}},
     *      operationId="indexStatesVehicles",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Vehicles list of State Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param State $state
     * @return JsonResponse
     */
    public function index(State $state): JsonResponse
    {
        $vehicles = $this->vehicleService->findAllByState($state);

        return $this->showAll($vehicles);
    }
}
