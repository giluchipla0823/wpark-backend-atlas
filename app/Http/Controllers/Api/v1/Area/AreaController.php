<?php

namespace App\Http\Controllers\Api\v1\Area;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Area\AreaStoreRequest;
use App\Http\Requests\Area\AreaUpdateRequest;
use App\Models\Area;
use App\Services\Application\Area\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AreaController extends ApiController
{
    /**
     * @var AreaService
     */
    private $areaService;

    public function __construct(
        AreaService $areaService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->areaService = $areaService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/areas",
     *      tags={"Areas"},
     *      summary="Areas List",
     *      description="List of areas",
     *      security={{"sanctum": {}}},
     *      operationId="indexAreas",
     *      @OA\Response(response=200, description="Area list Successfully"),
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
        $results = $this->areaService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/areas/datatables",
     *      tags={"Areas"},
     *      summary="Areas List with datatables",
     *      description="List of areas with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesAreas",
     *      @OA\Response(response=200, description="Area list Successfully with datatables"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource with datatables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatables(Request $request): JsonResponse
    {
        $results = $this->areaService->datatables($request);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/areas",
     *     tags={"Areas"},
     *     summary="Create New Area",
     *     description="Create New Area",
     *     security={{"sanctum": {} }},
     *     operationId="storeArea",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/AreaStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Area" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param AreaStoreRequest $request
     * @return JsonResponse
     */
    public function store(AreaStoreRequest $request): JsonResponse
    {
        $area = $this->areaService->create($request->all());

        return $this->successResponse($area, 'Area created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/areas/{id}",
     *     tags={"Areas"},
     *     summary="Show Area Details",
     *     description="Show Area Details",
     *     security={{"sanctum": {}}},
     *     operationId="showArea",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Area Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Area $area
     * @return JsonResponse
     */
    public function show(Area $area): JsonResponse
    {
        $area = $this->areaService->show($area);
        return $this->successResponse($area);
    }

    /**
     * @param AreaUpdateRequest $request
     * @param Area $area
     * @return JsonResponse
     */
    /* public function update(AreaUpdateRequest $request, Area $area): JsonResponse
    {
        $this->areaService->update($request->all(), $area->id);

        return $this->showMessage('Area updated successfully.');
    } */

    /**
     * @OA\Delete(
     *     path="/api/v1/areas/{id}",
     *     tags={"Areas"},
     *     summary="Delete Area",
     *     description="Delete Area and all associated parkings, rows and slots",
     *     security={{"sanctum": {}}},
     *     operationId="destroyArea",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Area successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Area $area
     * @return JsonResponse
     */
    public function destroy(Area $area): JsonResponse
    {
        $this->areaService->delete($area->id);

        return $this->showMessage('Area removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/areas/{id}",
     *     tags={"Areas"},
     *     summary="Restore Area",
     *     description="Restore Area and all associated parkings, rows and slots",
     *     security={{"sanctum": {}}},
     *     operationId="restoreArea",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Area restored successfully"),
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
        $this->areaService->restore($id);

        return $this->showMessage('Area restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
