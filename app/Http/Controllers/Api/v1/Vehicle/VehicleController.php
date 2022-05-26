<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Vehicle\VehicleDatatablesRequest;
use App\Models\Vehicle;
use App\Services\Application\Vehicle\VehicleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VehicleController extends ApiController
{
    /**
     * @var VehicleService
     */
    private $vehicleService;

    public function __construct(
        VehicleService $vehicleService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->vehicleService = $vehicleService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/vehicles",
     *      tags={"Vehicles"},
     *      summary="Vehicles List",
     *      description="List of vehicles",
     *      security={{"sanctum": {}}},
     *      operationId="indexVehicles",
     *      @OA\Response(response=200, description="Vehicle list Successfully"),
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
        $results = $this->vehicleService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/vehicles/datatables",
     *      tags={"Vehicles"},
     *      summary="Vehicles List to datatables plugin",
     *      description="List of vehicles to datatables plugin",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesVehicles",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/VehicleDatatablesRequest")
     *      ),
     *      @OA\Response(response=200, description="Vehicle list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource using datatables plugin.
     *
     * @param VehicleDatatablesRequest $request
     * @return JsonResponse
     */
    public function datatables(VehicleDatatablesRequest $request): JsonResponse
    {
        $results = $this->vehicleService->datatables($request);

        return $this->datatablesResponse($results);
    }

 //   /**
 //    * @param VehicleStageRequest $request
 //    * @return JsonResponse
 //    */
    /* public function store(VehicleStageRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->create($request->all());

        return $this->successResponse($vehicle, 'Vehicle created successfully.', Response::HTTP_CREATED);
    } */

    /**
     * @OA\GET(
     *     path="/api/v1/vehicles/{id}",
     *     tags={"Vehicles"},
     *     summary="Show Vehicle Details",
     *     description="Show Vehicle Details",
     *     security={{"sanctum": {}}},
     *     operationId="showVehicle",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Vehicle Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle = $this->vehicleService->show($vehicle);

        return $this->successResponse($vehicle);
    }

 //   /**
 //    * @param VehicleStageRequest $request
 //    * @param Vehicle $vehicle
 //    * @return JsonResponse
 //    */
    /* public function update(VehicleStageRequest $request, Vehicle $vehicle): JsonResponse
    {
        $this->vehicleService->update($request->all(), $vehicle->id);

        return $this->showMessage('Vehicle updated successfully.');
    } */

    /**
     * @OA\Delete(
     *     path="/api/v1/vehicles/{id}",
     *     tags={"Vehicles"},
     *     summary="Delete Vehicle",
     *     description="Delete Vehicle",
     *     security={{"sanctum": {}}},
     *     operationId="destroyVehicle",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Vehicle successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->vehicleService->delete($vehicle->id);

        return $this->showMessage('Vehicle removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/vehicles/{id}",
     *     tags={"Vehicles"},
     *     summary="Restore Vehicle",
     *     description="Restore Vehicle",
     *     security={{"sanctum": {}}},
     *     operationId="restoreVehicle",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Vehicle restored successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Restores the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $this->vehicleService->restore($id);

        return $this->showMessage('Vehicle restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/vehicles/{vehicle}/detail",
     *     tags={"Vehicles"},
     *     summary="Show Vehicle info Details",
     *     description="Show Vehicle info Details",
     *     security={{"sanctum": {}}},
     *     operationId="infoVehicle",
     *     @OA\Response(response=200, description="Show Vehicle info Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     * Display the specified resource.
     *
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function detail(Vehicle $vehicle): JsonResponse
    {
        $vehicle = $this->vehicleService->detail($vehicle);
        return $this->successResponse($vehicle);
    }

    /**
     * @return JsonResponse
     */
    public function massiveChangeData(): JsonResponse
    {
        return $this->showMessage("Cambios realizados.");
    }

    /**
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function changePosition(Vehicle $vehicle): JsonResponse
    {
        return $this->showMessage("Cambios realizados.");
    }

    /**
     * @param string $vin
     * @return JsonResponse
     * @throws Exception
     */
    public function searchByVin(string $vin): JsonResponse
    {
        $vehicle = $this->vehicleService->searchByVin($vin);

        return $this->successResponse($vehicle);
    }

}
