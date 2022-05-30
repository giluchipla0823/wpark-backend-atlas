<?php

namespace App\Http\Controllers\Api\v1\RouteType;

use App\Http\Controllers\ApiController;
use App\Models\RouteType;
use App\Services\Application\Carrier\CarrierService;
use Illuminate\Http\JsonResponse;

class RouteTypeCarrierController extends ApiController
{

    /**
     * @var CarrierService
     */
    private $carrierService;

    public function __construct(
        CarrierService $carrierService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->carrierService = $carrierService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/routes-types/{id}/carriers",
     *      tags={"RoutesTypes"},
     *      summary="List of carriers given a route type",
     *      description="List of carriers given a route type",
     *      security={{"sanctum": {}}},
     *      operationId="indexRoutesTypesCarriers",
     *      @OA\Response(response=200, description="Carriers list successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param RouteType $routeType
     * @return JsonResponse
     */
    public function index(RouteType $routeType): JsonResponse
    {
        $results = $this->carrierService->findAllByRouteType($routeType);

        return $this->successResponse($results);
    }
}
