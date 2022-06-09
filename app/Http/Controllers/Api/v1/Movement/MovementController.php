<?php

namespace App\Http\Controllers\Api\v1\Movement;

use Exception;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Movement\MovementCancelRequest;
use App\Http\Requests\Movement\MovementReloadRequest;
use App\Services\Application\Movement\MovementService;
use App\Http\Requests\Movement\MovementDatatablesRequest;

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
     * @throws Exception
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
     * @throws Exception
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
     * @param MovementCancelRequest $request
     * @param Movement $movement
     * @return JsonResponse
     * @throws Exception
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
     * @throws Exception
     */
    public function reload(MovementReloadRequest $request): JsonResponse
    {
        $response = $this->movementService->reload($request->all());
        return $this->successResponse($response);
    }

}
