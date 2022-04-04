<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use Illuminate\Http\JsonResponse;
use App\Services\Vehicle\VehicleStageService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Vehicle\VehicleStageRequest;

class VehicleStageController extends ApiController
{
    // TODO: Servicio para crear/actualizar vehículos desde la api de Ford
    /**
     * @var VehicleStageService
     */
    private $vehicleStageService;

    public function __construct(
        VehicleStageService $vehicleStageService
    )
    {
        //$this->middleware('role:Super-Admin|admin');
        $this->vehicleStageService = $vehicleStageService;
    }

    public function vehicleStage(VehicleStageRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleStageService->vehicleStage($request->all());

        return $this->successResponse($vehicle, 'Vehicle created or updated successfully.', Response::HTTP_CREATED);
    }

}
