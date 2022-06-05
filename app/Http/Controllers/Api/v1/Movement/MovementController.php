<?php

namespace App\Http\Controllers\Api\v1\Movement;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Movement\MovementCancelRequest;
use App\Http\Requests\Movement\MovementReloadRequest;
use App\Http\Requests\Movement\MovementDatatablesRequest;
use App\Http\Requests\Movement\MovementStoreRequest;
use App\Models\Movement;
use App\Services\Application\Movement\MovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovementController extends ApiController
{
    /**
     * @var MovementService
     */
    private $movementService;

    public function __construct(
        MovementService $movementService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->movementService = $movementService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/movements",
     *      tags={"Movements"},
     *      summary="Movements List",
     *      description="List of movements",
     *      security={{"sanctum": {}}},
     *      operationId="indexMovements",
     *      @OA\Response(response=200, description="Movement list Successfully"),
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
        $results = $this->movementService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/movements/datatables",
     *      tags={"Movements"},
     *      summary="Movements List",
     *      description="List of movements with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesMovements",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/MovementDatatablesRequest")
     *      ),
     *      @OA\Response(response=200, description="Movements list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param MovementDatatablesRequest $request
     * @return JsonResponse
     */
    public function datatables(MovementDatatablesRequest $request): JsonResponse
    {
        $results = $this->movementService->datatables($request);

        return $this->datatablesResponse($results);
    }

    // /**
    // * @OA\POST(
    // *     path="/api/v1/movements",
    // *     tags={"Movements"},
    // *     summary="Create New Movement",
    // *     description="Create New Movement",
    // *     security={{"sanctum": {} }},
    // *     operationId="storeMovement",
    // *     @OA\RequestBody(
    // *          required=true,
    // *          @OA\JsonContent(ref="#/components/schemas/MovementStoreRequest")
    // *     ),
    // *     @OA\Response(response=201, description="Create New Movement" ),
    // *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
    // *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    // *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    // *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    // * )
    // *
    // * Store a newly created resource in storage.
    // *
    // * @param MovementStoreRequest $request
    // * @return JsonResponse
    // */
    //public function store(MovementStoreRequest $request): JsonResponse
    //{
    //    $movement = $this->movementService->create($request->all());
    //
    //    return $this->successResponse($movement, 'Movement created successfully.', Response::HTTP_CREATED);
    //}

    /**
     * @OA\GET(
     *     path="/api/v1/movements/{id}",
     *     tags={"Movements"},
     *     summary="Show Movement Details",
     *     description="Show Movement Details",
     *     security={{"sanctum": {}}},
     *     operationId="showMovement",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Añadir bloques, condiciones",
     *         example="blocks,conditions",
     *         required=false
     *     ),
     *     @OA\Parameter(
     *         name="extra_includes",
     *         in="query",
     *         description="Añadir valores de condiciones",
     *         example="conditions.values",
     *         required=false
     *     ),
     *     @OA\Response(response=200, description="Show Movement Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Movement $movement
     * @return JsonResponse
     */
    public function show(Movement $movement): JsonResponse
    {
        $movement = $this->movementService->show($movement);
        return $this->successResponse($movement);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/movements/{id}/confirm",
     *     tags={"Movements"},
     *     summary="Confirm Movement",
     *     description="Confirm Movement",
     *     security={{"sanctum": {}}},
     *     operationId="confirmMovement",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Confirm Movement" ),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Confirm the specified movement.
     *
     * @param Movement $movement
     * @return JsonResponse
     */
    public function confirmMovement(Movement $movement): JsonResponse
    {
        $this->movementService->confirmMovement($movement);

        return $this->showMessage('Movement confirmed.');
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/movements/{id}/cancel",
     *     tags={"Movements"},
     *     summary="Cancel Movement",
     *     description="Cancel Movement",
     *     security={{"sanctum": {}}},
     *     operationId="cancelMovement",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/MovementCancelRequest")
     *      ),
     *     @OA\Response(response=200, description="Cancel Movement" ),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Cancel the specified movement.
     *
     * @param Movement $movement
     * @return JsonResponse
     */
    public function cancelMovement(MovementCancelRequest $request, Movement $movement): JsonResponse
    {
        $this->movementService->cancelMovement($request->only('comments'), $movement);

        return $this->showMessage('Movement canceled.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/movement/reload",
     *      tags={"Movements"},
     *      summary="Movement Reload",
     *      description="Movement reload",
     *      security={{"sanctum": {}}},
     *      operationId="reloadMovement",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/MovementReloadRequest")
     *      ),
     *      @OA\Response(response=200, description="Movement Reload Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param MovementReloadRequest $request
     * @return JsonResponse
     */
    public function reload(MovementReloadRequest $request): JsonResponse
    {
        $response = $this->movementService->reload($request->all());
        return $this->successResponse($response);
    }

    ///**
    // * @OA\PUT(
    // *     path="/api/v1/movements/{id}",
    // *     tags={"Movements"},
    // *     summary="Update Movement",
    // *     description="Update Movement",
    // *     security={{"sanctum": {}}},
    // *     operationId="updateMovement",
    // *     @OA\Parameter(ref="#/components/parameters/id"),
    // *     @OA\RequestBody(
    // *          required=true,
    // *          @OA\JsonContent(ref="#/components/schemas/MovementUpdateRequest")
    // *     ),
    // *     @OA\Response(response=200, description="Update Movement" ),
    // *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
    // *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
    // *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    // *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    // *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    // * )
    // *
    // * Update the specified resource in storage.
    // *
    // * @param Request $request
    // * @param Movement $movement
    // * @return JsonResponse
    // */
    /* public function update(Request $request, Movement $movement): JsonResponse
    {
        $this->movementService->update($request->all(), $movement->id);

        return $this->showMessage('Movement updated successfully.');
    } */

    // /**
    //  * @OA\Delete(
    //  *     path="/api/v1/movements/{id}",
    //  *     tags={"Movements"},
    //  *     summary="Delete Movement",
    //  *     description="Delete Movement",
    //  *     security={{"sanctum": {}}},
    //  *     operationId="destroyMovement",
    //  *     @OA\Parameter(ref="#/components/parameters/id"),
    //  *     @OA\Response(response=204, description="Delete Movement successfully"),
    //  *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
    //  *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    //  *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    //  *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    //  * )
    //  *
    //  * Remove the specified resource from storage.
    //  *
    //  * @param Movement $movement
    //  * @return JsonResponse
    //  */
    /* public function destroy(Movement $movement): JsonResponse
    {
        $this->movementService->delete($movement->id);

        return $this->showMessage('Movement removed successfully.', Response::HTTP_NO_CONTENT);
    } */

    // /**
    //  * @OA\PATCH(
    //  *     path="/api/v1/movements/{id}",
    //  *     tags={"Movements"},
    //  *     summary="Restore Movement",
    //  *     description="Restore Movement",
    //  *     security={{"sanctum": {}}},
    //  *     operationId="restoreMovement",
    //  *     @OA\Parameter(ref="#/components/parameters/id"),
    //  *     @OA\Response(response=204, description="Movement restored successfully"),
    //  *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
    //  *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    //  *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    //  *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    //  * )
    //  *
    //  * Restores the specified resource from storage.
    //  *
    //  * @param int $id
    //  * @return JsonResponse
    //  */
    /* public function restore(int $id): JsonResponse
    {
        $this->movementService->restore($id);

        return $this->showMessage('Movement restored successfully.', Response::HTTP_NO_CONTENT);
    } */


}
