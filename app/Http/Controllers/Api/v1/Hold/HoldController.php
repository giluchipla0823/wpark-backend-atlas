<?php

namespace App\Http\Controllers\Api\v1\Hold;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Hold\HoldStoreRequest;
use App\Http\Requests\Hold\HoldUpdateRequest;
use App\Models\Hold;
use App\Services\Application\Hold\HoldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoldController extends ApiController
{
    /**
     * @var HoldService
     */
    private $holdService;

    public function __construct(
        HoldService $holdService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->holdService = $holdService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/holds",
     *      tags={"Holds"},
     *      summary="Holds List",
     *      description="List of holds",
     *      security={{"sanctum": {}}},
     *      operationId="indexHolds",
     *      @OA\Response(response=200, description="Hold list Successfully"),
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
        $results = $this->holdService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/holds/datatables",
     *      tags={"Holds"},
     *      summary="Holds List with datatables",
     *      description="List of holds with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesHolds",
     *      @OA\Response(response=200, description="Hold list with datatables Successfully"),
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
        $results = $this->holdService->datatables($request);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/holds",
     *     tags={"Holds"},
     *     summary="Create New Hold",
     *     description="Create New Hold",
     *     security={{"sanctum": {} }},
     *     operationId="storeHold",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/HoldStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Hold" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param HoldStoreRequest $request
     * @return JsonResponse
     */
    public function store(HoldStoreRequest $request): JsonResponse
    {
        $hold = $this->holdService->create($request->all());

        return $this->successResponse($hold, 'Hold created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/holds/{id}",
     *     tags={"Holds"},
     *     summary="Show Hold Details",
     *     description="Show Hold Details",
     *     security={{"sanctum": {}}},
     *     operationId="showHold",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Hold Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Hold $hold
     * @return JsonResponse
     */
    public function show(Hold $hold): JsonResponse
    {
        $hold = $this->holdService->show($hold);
        return $this->successResponse($hold);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/holds/{id}",
     *     tags={"Holds"},
     *     summary="Update Hold",
     *     description="Update Hold",
     *     security={{"sanctum": {}}},
     *     operationId="updateHold",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/HoldUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Hold" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param HoldUpdateRequest $request
     * @param Hold $hold
     * @return JsonResponse
     */
    public function update(HoldUpdateRequest $request, Hold $hold): JsonResponse
    {
        $this->holdService->update($request->all(), $hold->id);

        return $this->showMessage('Hold updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/holds/{id}",
     *     tags={"Holds"},
     *     summary="Delete Hold",
     *     description="Delete Hold",
     *     security={{"sanctum": {}}},
     *     operationId="destroyHold",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Hold successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Hold $hold
     * @return JsonResponse
     */
    public function destroy(Hold $hold): JsonResponse
    {
        $this->holdService->delete($hold->id);

        return $this->showMessage('Hold removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/holds/{id}",
     *     tags={"Holds"},
     *     summary="Restore Hold",
     *     description="Restore Hold",
     *     security={{"sanctum": {}}},
     *     operationId="restoreHold",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Hold restored successfully"),
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
        $this->holdService->restore($id);

        return $this->showMessage('Hold restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/holds/{id}/toggle-active",
     *     tags={"Holds"},
     *     summary="Toggle Active Hold",
     *     description="Toggle Active Hold",
     *     security={{"sanctum": {}}},
     *     operationId="toggleActiveHold",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Hold toggle active successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Toggle active the specified resource from storage.
     *
     * @param Hold $hold
     * @return JsonResponse
     */
    public function toggleActive(Hold $hold): JsonResponse
    {
        $active = $this->holdService->toggleActive($hold);

        $message = $active === 0 ? 'El hold se desactivó correctamente.' : 'El hold se activó correctamente.';

        return $this->showMessage($message);
    }


}
