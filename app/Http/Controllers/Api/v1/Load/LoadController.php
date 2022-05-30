<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Load\LoadValidateRequest;
use App\Services\Application\Load\LoadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoadController extends ApiController
{
    /**
     * @var LoadService
     */
    private $loadService;

    public function __construct(
        LoadService $loadService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->loadService = $loadService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/loads/check-vehicles",
     *      tags={"Loads"},
     *      summary="Check Vehicles",
     *      description="Validate if the vehicles have the same destination as the carrier on their routes",
     *      security={{"sanctum": {}}},
     *      operationId="checkVehicles",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoadValidateRequest")
     *     ),
     *      @OA\Response(response=200, description="Check Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Check the vehicles meet the conditions to be in a LOAD
     *
     * @param LoadValidateRequest $request
     * @return JsonResponse
     */
    public function checkVehicles(LoadValidateRequest $request): JsonResponse
    {
        $res = $this->loadService->checkVehicles($request->all());
        return $this->successResponse($res);
    }
}
