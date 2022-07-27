<?php

namespace App\Http\Controllers\Api\External;

use App\Exceptions\owner\BadRequestException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Services\External\RecirculationService;

class RecirculationController extends ApiController
{
    /**
     * @var RecirculationService
     */
    private $recirculationService;

    public function __construct(
        RecirculationService $recirculationService
    )
    {
        $this->recirculationService = $recirculationService;
    }

    /**
     * @OA\GET(
     *      path="/api/external/recirculations/{vin}",
     *      tags={"Recirculations"},
     *      summary="Recirculations Get",
     *      description="SOAP FORD",
     *      security={{"sanctum": {}}},
     *      operationId="recirculationsGet",
     *      @OA\Parameter(
     *         name="vin",
     *         in="path",
     *         description="Vin de vehículo",
     *         example="WF0FXXWPMFNJ49207",
     *         required=true
     *      ),
     *      @OA\Response(response=200, description="Data FORD"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param string $vin
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function get(string $vin): JsonResponse
    {
        $response = $this->recirculationService->GetVehicleDestination($vin);

        return $this->successResponse($response);
    }
}
