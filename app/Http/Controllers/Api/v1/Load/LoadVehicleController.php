<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Load\LoadValidateRequest;
use App\Models\Load;
use App\Models\Vehicle;
use App\Services\Application\Load\LoadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadVehicleController extends ApiController
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
     *      path="/api/v1/loads/{id}/vehicles/datatables",
     *      tags={"Loads", "Vehicles"},
     *      summary="Vehicles List of Load with datatables",
     *      description="Vehicles List of Load with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesLoadsVehicles",
     *      @OA\Response(response=200, description="Vehicles of load list with datatables Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Load $load
     * @return JsonResponse
     */
    public function datatables(Load $load): JsonResponse
    {
        $results = $this->loadService->datatablesVehicles($load);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/loads/{load}/vehicles/{vehicle}/unlink",
     *      tags={"Loads", "Vehicles"},
     *      summary="Delete Vehicle of Load",
     *      description="Delete Vehicle of Load",
     *      security={{"sanctum": {}}},
     *      operationId="unlinkVehicleLoad",
     *      @OA\Response(response=200, description="Delete vehicle of load Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Unlink Vehicle of Load
     *
     * @param Load $load
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function unlinkVehicle(Load $load, Vehicle $vehicle): JsonResponse
    {
        $this->loadService->unlinkVehicle($load, $vehicle);

        return $this->showMessage('El vehículo seleccionado ha sido desvinculado del load.');
    }
}
