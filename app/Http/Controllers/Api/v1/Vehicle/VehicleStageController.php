<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Vehicle\VehicleStageRequest;
use App\Services\Application\Vehicle\VehicleStageService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class VehicleStageController extends ApiController
{
    /**
     * @var VehicleStageService
     */
    private $vehicleStageService;

    public function __construct(
        VehicleStageService $vehicleStageService
    ) {
        $this->middleware('basic.auth');
        $this->vehicleStageService = $vehicleStageService;
    }

    /**
     * @OA\POST(
     *     path="/api/tracking-points",
     *     tags={"Tracking Points"},
     *     summary="Create or update vehicle from stage",
     *     description="Create or update vehicle from stage",
     *     security={{"basic": {}}},
     *     operationId="tracking-points",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/VehicleStageRequest")
     *     ),
     *     @OA\Response(response=201, description="OK"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * ST7 Api.
     *
     * @param VehicleStageRequest $request
     * @return JsonResponse
     * @throws FordStandardErrorException
     * @throws GuzzleException
     */
    public function vehicleStage(VehicleStageRequest $request): JsonResponse
    {
        $this->vehicleStageService->vehicleStage($request->all());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
