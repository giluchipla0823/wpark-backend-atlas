<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Vehicle\VehicleManualStoreRequest;
use App\Services\Application\Vehicle\VehicleManualStoreService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class VehicleManualStoreController extends ApiController
{
    /**
     * @var VehicleManualStoreService
     */
    private $vehicleManualStoreService;

    public function __construct(
        VehicleManualStoreService $vehicleManualStoreService
    )
    {
        $this->vehicleManualStoreService = $vehicleManualStoreService;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/vehicles/create-manual",
     *     tags={"Vehicles"},
     *     summary="Create New Vehicle",
     *     description="Create New Vehicle",
     *     security={{"sanctum": {} }},
     *     operationId="storeColor",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/VehicleManualStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Vehicle" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param VehicleManualStoreRequest $request
     * @return JsonResponse
     */
    public function __invoke(VehicleManualStoreRequest $request): JsonResponse
    {
        $this->vehicleManualStoreService->create($request->all());

        return $this->showMessage('Vehicle created successfully.', Response::HTTP_CREATED);
    }
}
