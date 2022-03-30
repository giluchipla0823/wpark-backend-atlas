<?php

namespace App\Http\Controllers\Api\v1\Zone;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Zone\ZoneService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Zone\ZoneStoreRequest;
use App\Http\Requests\Zone\ZoneUpdateRequest;

class ZoneController extends ApiController
{
    /**
     * @var ZoneService
     */
    private $zoneService;

    public function __construct(
        ZoneService $zoneService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->zoneService = $zoneService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/zones",
     *      tags={"Zones"},
     *      summary="Zones List",
     *      description="List of zones",
     *      security={{"sanctum": {}}},
     *      operationId="indexZones",
     *      @OA\Response(response=200, description="Zone list Successfully"),
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
        $results = $this->zoneService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/zones",
     *     tags={"Zones"},
     *     summary="Create New Zone",
     *     description="Create New Zone",
     *     security={{"sanctum": {} }},
     *     operationId="storeZone",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ZoneStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Zone" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ZoneStoreRequest $request
     * @return JsonResponse
     */
    public function store(ZoneStoreRequest $request): JsonResponse
    {
        $zone = $this->zoneService->create($request->all());

        return $this->successResponse($zone, 'Zone created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/zones/{id}",
     *     tags={"Zones"},
     *     summary="Show Zone Details",
     *     description="Show Zone Details",
     *     security={{"sanctum": {}}},
     *     operationId="showZone",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Zone Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Zone $zone
     * @return JsonResponse
     */
    public function show(Zone $zone): JsonResponse
    {
        $zone = $this->zoneService->show($zone);
        return $this->successResponse($zone);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/zones/{id}",
     *     tags={"Zones"},
     *     summary="Update Zone",
     *     description="Update Zone",
     *     security={{"sanctum": {}}},
     *     operationId="updateZone",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ZoneUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Zone" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ZoneUpdateRequest $request
     * @param Zone $zone
     * @return JsonResponse
     */
    public function update(ZoneUpdateRequest $request, Zone $zone): JsonResponse
    {
        $this->zoneService->update($request->all(), $zone->id);

        return $this->showMessage('Zone updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/zones/{id}",
     *     tags={"Zones"},
     *     summary="Delete Zone",
     *     description="Delete Zone",
     *     security={{"sanctum": {}}},
     *     operationId="destroyZone",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Zone successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Zone $zone
     * @return JsonResponse
     */
    public function destroy(Zone $zone): JsonResponse
    {
        $this->zoneService->delete($zone->id);

        return $this->showMessage('Zone removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/zones/{id}",
     *     tags={"Zones"},
     *     summary="Restore Zone",
     *     description="Restore Zone",
     *     security={{"sanctum": {}}},
     *     operationId="restoreZone",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Zone restored successfully"),
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
        $this->zoneService->restore($id);

        return $this->showMessage('Zone restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
