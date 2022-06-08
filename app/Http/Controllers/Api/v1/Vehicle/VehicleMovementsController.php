<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use App\Http\Controllers\ApiController;
use App\Services\Application\Vehicle\VehicleMovementsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VehicleMovementsController extends ApiController
{
    /**
     * @var VehicleMovementsService
     */
    private $vehicleMovementsService;

    public function __construct(
        VehicleMovementsService $vehicleMovementsService
    ) {
        $this->middleware('role:Super-Admin|admin');
        $this->vehicleMovementsService = $vehicleMovementsService;
    }

    /**
     * @OA\GET(
     *     path="/api/v1/vehicles/vin/{vin}",
     *     tags={"Vehicles"},
     *     summary="Match rules from vehicles",
     *     description="Match rules from vehicles",
     *     security={{"sanctum": {}}},
     *     operationId="match-rules",
     *     @OA\Parameter(ref="#/components/parameters/vin"),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display rules from vehicles.
     *
     * @param string $vin
     * @return JsonResponse
     * @throws Exception
     */
    public function vehicleMatchRules(string $vin): JsonResponse
    {
        $vehicle = $this->vehicleMovementsService->vehicleIdentify($vin);

        $response = $this->vehicleMovementsService->vehicleMatchRules($vehicle);

        return $this->successResponse($response);
    }
}
