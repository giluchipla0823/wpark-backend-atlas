<?php

namespace App\Http\Controllers\Api\External;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Services\External\RecirculationService;

class RecirculationController extends ApiController
{
    /**
     * @OA\GET(
     *      path="/api/v1/recirculations/{vin}",
     *      tags={"Recirculations"},
     *      summary="Recirculations Get",
     *      description="SOAP FORD",
     *      security={{"sanctum": {}}},
     *      operationId="recirculationsGet",
     *      @OA\Response(response=200, description="Data FORD"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param string $vin
     * @return JsonResponse
     */
    public function get(string $vin): JsonResponse
    {
        $service = new RecirculationService;

        $response = $service->GetVehicleDestination($vin);

        return $this->successResponse($response);
    }
}
