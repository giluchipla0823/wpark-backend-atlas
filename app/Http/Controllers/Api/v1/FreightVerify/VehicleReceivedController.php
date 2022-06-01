<?php

namespace App\Http\Controllers\Api\v1\FreightVerify;

use App\Http\Controllers\ApiController;
use App\Http\Requests\FreightVerify\VehicleReceivedRequest;
use App\Services\External\FreightVerify\FreightVerifyService;
use Symfony\Component\HttpFoundation\Response;

class VehicleReceivedController extends ApiController
{
    /**
     * @var FreightVerifyService
     */
    private $freightVerifyService;

    public function __construct(FreightVerifyService $freightVerifyService) {
        $this->freightVerifyService = $freightVerifyService;
    }
    /**
     * Handle the incoming request.
     *
     * @OA\POST(
     *      path="/api/v1/freight-verify/vehicle-received",
     *      tags={"FreightVerify"},
     *      summary="Envío de milestone de Vehicle Received a FreightVerify",
     *      description="Envío de milestone de Vehicle Received a FreightVerify",
     *      security={{"sanctum": {}}},
     *      operationId="sendVehicleReceivedMilestone",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/VehicleReceivedRequest")
     *      ),
     *      @OA\Response(response=202, description="FreightVerify response data"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Envío de milestone de Vehicle Received a FreightVerify.
     *
     * @param  \App\Http\Requests\FreightVerify\VehicleReceivedRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(VehicleReceivedRequest $request)
    {
        $response = $this->freightVerifyService->sendVehicleReceived($request->vin, $request->except('vin'));

        return $this->successResponse($response, $response->msg ?? 'Success', Response::HTTP_ACCEPTED);
    }
}
