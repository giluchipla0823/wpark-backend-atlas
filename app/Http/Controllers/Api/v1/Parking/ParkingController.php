<?php

namespace App\Http\Controllers\Api\v1\Parking;

use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Parking\ParkingService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Parking\ParkingStoreRequest;
use App\Http\Requests\Parking\ParkingUpdateRequest;

class ParkingController extends ApiController
{
    /**
     * @var ParkingService
     */
    private $parkingService;

    public function __construct(
        ParkingService $parkingService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->parkingService = $parkingService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/parkings",
     *      tags={"Parkings"},
     *      summary="Parkings List",
     *      description="List of parkings",
     *      security={{"sanctum": {}}},
     *      operationId="indexParkings",
     *      @OA\Response(response=200, description="Parking list Successfully"),
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
        $results = $this->parkingService->all($request);

        return $this->showAll($results);
    }

//    /**
//     * @param ParkingStoreRequest $request
//     * @return JsonResponse
//     */
    /* public function store(ParkingStoreRequest $request): JsonResponse
    {
        $parking = $this->parkingService->create($request->all());

        return $this->successResponse($parking, 'Parking created successfully.', Response::HTTP_CREATED);
    } */

    /**
     * @OA\GET(
     *     path="/api/v1/parkings/{id}",
     *     tags={"Parkings"},
     *     summary="Show Parking Details",
     *     description="Show Parking Details",
     *     security={{"sanctum": {}}},
     *     operationId="showParking",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Parking Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Parking $parking
     * @return JsonResponse
     */
    public function show(Parking $parking): JsonResponse
    {
        $parking = $this->parkingService->show($parking);
        return $this->successResponse($parking);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/parkings/{id}",
     *     tags={"Parkings"},
     *     summary="Update Parking",
     *     description="Update Parking",
     *     security={{"sanctum": {}}},
     *     operationId="updateParking",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ParkingUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Parking" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ParkingUpdateRequest $request
     * @param Parking $parking
     * @return JsonResponse
     */
    public function update(ParkingUpdateRequest $request, Parking $parking): JsonResponse
    {
        $this->parkingService->update($request->all(), $parking->id);

        return $this->showMessage('Parking updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/parkings/{id}",
     *     tags={"Parkings"},
     *     summary="Delete Parking",
     *     description="Delete Parking",
     *     security={{"sanctum": {}}},
     *     operationId="destroyParking",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Parking successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Parking $parking
     * @return JsonResponse
     */
    public function destroy(Parking $parking): JsonResponse
    {
        $this->parkingService->delete($parking->id);

        return $this->showMessage('Parking removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/parkings/{id}",
     *     tags={"Parkings"},
     *     summary="Restore Parking",
     *     description="Restore Parking",
     *     security={{"sanctum": {}}},
     *     operationId="restoreParking",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Parking restored successfully"),
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
        $this->parkingService->restore($id);

        return $this->showMessage('Parking restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/parkings/{id}/toggle-active",
     *     tags={"Parkings"},
     *     summary="Toggle Active Parking",
     *     description="Toggle Active Parking",
     *     security={{"sanctum": {}}},
     *     operationId="toggleActiveParking",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Parking toogle active successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Toggle active the specified resource from storage.
     *
     * @param Parking $parking
     * @return JsonResponse
     */
    public function toggleActive(Parking $parking): JsonResponse
    {
        $active = $this->parkingService->toggleActive($parking);

        $message = $active === 0 ? 'El parking se desactivó correctamente.' : 'El parking se activó correctamente.';

        return $this->showMessage($message);
    }

}
