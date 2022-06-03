<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Load\LoadValidateRequest;
use App\Models\Load;
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

    /**
     * @OA\Get(
     *      path="/api/v1/loads",
     *      tags={"Designs"},
     *      summary="Loads List",
     *      description="List of loads",
     *      security={{"sanctum": {}}},
     *      operationId="indexLoads",
     *      @OA\Response(response=200, description="Design list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $results = $this->loadService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/loads/datatables",
     *      tags={"Loads"},
     *      summary="Loads List with datatables",
     *      description="List of Loads with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesLoads",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Load list with datatables Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatables(Request $request): JsonResponse
    {
        $results = $this->loadService->datatables($request);

        return $this->datatablesResponse($results);
    }
}
