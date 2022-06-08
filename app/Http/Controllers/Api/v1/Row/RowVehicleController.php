<?php

namespace App\Http\Controllers\Api\v1\Row;

use App\Exceptions\owner\BadRequestException;
use App\Http\Controllers\ApiController;
use App\Models\Row;
use App\Services\Application\Vehicle\VehicleService;
use Illuminate\Http\JsonResponse;

class RowVehicleController extends ApiController
{

    /**
     * @var VehicleService
     */
    private $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->middleware('role:Super-Admin|admin');
        $this->vehicleService = $vehicleService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/rows/{id}/vehicles",
     *      tags={"Rows"},
     *      summary="Vehicles List of row",
     *      description="Vehicles List of row",
     *      security={{"sanctum": {}}},
     *      operationId="indexRowsVehicles",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Vehicles list of Row Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Row $row
     * @return JsonResponse
     */
    public function index(Row $row): JsonResponse
    {
        throw new BadRequestException("hola");
        $vehicles = $this->vehicleService->findAllByRow($row);

        return $this->showAll($vehicles);
    }
}
