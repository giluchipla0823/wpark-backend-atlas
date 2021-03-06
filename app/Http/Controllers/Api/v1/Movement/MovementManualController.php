<?php

namespace App\Http\Controllers\Api\v1\Movement;

use Exception;
use App\Exceptions\owner\BadRequestException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Movement\MovementFilteredPositionsRequest;
use App\Services\Application\Movement\MovementManualService;
use App\Http\Requests\Movement\MovementStoreRequest;
use App\Services\Application\Movement\MovementService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MovementManualController extends ApiController
{
    /**
     * @var MovementManualService
     */
    private $movementManualService;
    /**
     * @var MovementService
     */
    private $movementService;

    public function __construct(
        MovementManualService $movementManualService,
        MovementService $movementService
    ) {
        $this->middleware('role:Super-Admin|admin');
        $this->movementManualService = $movementManualService;
        $this->movementService = $movementService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/movements/manual/filtered-positions",
     *      tags={"Movements"},
     *      summary="Movements Filtered Positions",
     *      description="Movements Filtered Positions",
     *      security={{"sanctum": {}}},
     *      operationId="filteredPositionsMovements",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/MovementFilteredPositionsRequest")
     *      ),
     *      @OA\Response(response=200, description="Movement FilteredPositions Successfully"),
     *      @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param MovementFilteredPositionsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function filteredPositions(MovementFilteredPositionsRequest $request): JsonResponse
    {
        $response = $this->movementManualService->filteredPositions($request->all());
        return $this->successResponse($response);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/movements/manual",
     *     tags={"Movements"},
     *     summary="Create Manual Movement",
     *     description="Create Manual Movement",
     *     security={{"sanctum": {} }},
     *     operationId="manualMovement",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/MovementStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create Manual Movement" ),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param MovementStoreRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function manual(MovementStoreRequest $request): JsonResponse
    {
        $this->movementService->manual($request->all());

        return $this->showMessage('Movement created successfully.', Response::HTTP_CREATED);
    }

}
