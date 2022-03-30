<?php

namespace App\Http\Controllers\Api\v1\Parking;

use App\Models\ParkingType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Parking\ParkingTypeService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Parking\ParkingTypeStoreRequest;
use App\Http\Requests\Parking\ParkingTypeUpdateRequest;

class ParkingTypeController extends ApiController
{
    /**
     * @var ParkingTypeService
     */
    private $parkingTypeService;

    public function __construct(
        ParkingTypeService $parkingTypeService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->parkingTypeService = $parkingTypeService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/parking-types",
     *      tags={"ParkingTypes"},
     *      summary="ParkingTypes List",
     *      description="List of parkingTypes",
     *      security={{"sanctum": {}}},
     *      operationId="indexParkingTypes",
     *      @OA\Response(response=200, description="ParkingType list Successfully"),
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
        $results = $this->parkingTypeService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/parking-types",
     *     tags={"ParkingTypes"},
     *     summary="Create New ParkingType",
     *     description="Create New ParkingType",
     *     security={{"sanctum": {} }},
     *     operationId="storeParkingType",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ParkingTypeStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New ParkingType" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ParkingTypeStoreRequest $request
     * @return JsonResponse
     */
    public function store(ParkingTypeStoreRequest $request): JsonResponse
    {
        $parkingType = $this->parkingTypeService->create($request->all());

        return $this->successResponse($parkingType, 'ParkingType created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/parking-types/{id}",
     *     tags={"ParkingTypes"},
     *     summary="Show ParkingType Details",
     *     description="Show ParkingType Details",
     *     security={{"sanctum": {}}},
     *     operationId="showParkingType",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show ParkingType Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param ParkingType $parkingType
     * @return JsonResponse
     */
    public function show(ParkingType $parkingType): JsonResponse
    {
        $parkingType = $this->parkingTypeService->show($parkingType);
        return $this->successResponse($parkingType);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/parking-types/{id}",
     *     tags={"ParkingTypes"},
     *     summary="Update ParkingType",
     *     description="Update ParkingType",
     *     security={{"sanctum": {}}},
     *     operationId="updateParkingType",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ParkingTypeUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update ParkingType" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ParkingTypeUpdateRequest $request
     * @param ParkingType $parkingType
     * @return JsonResponse
     */
    public function update(ParkingTypeUpdateRequest $request, ParkingType $parkingType): JsonResponse
    {
        $this->parkingTypeService->update($request->all(), $parkingType->id);

        return $this->showMessage('ParkingType updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/parking-types/{id}",
     *     tags={"ParkingTypes"},
     *     summary="Delete ParkingType",
     *     description="Delete ParkingType",
     *     security={{"sanctum": {}}},
     *     operationId="destroyParkingType",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete ParkingType successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param ParkingType $parkingType
     * @return JsonResponse
     */
    public function destroy(ParkingType $parkingType): JsonResponse
    {
        $this->parkingTypeService->delete($parkingType->id);

        return $this->showMessage('ParkingType removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/parking-types/{id}",
     *     tags={"ParkingTypes"},
     *     summary="Restore ParkingType",
     *     description="Restore ParkingType",
     *     security={{"sanctum": {}}},
     *     operationId="restoreParkingType",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="ParkingType restored successfully"),
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
        $this->parkingTypeService->restore($id);

        return $this->showMessage('ParkingType restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
