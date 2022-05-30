<?php

namespace App\Http\Controllers\Api\v1\Movement;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Movement\MovementRecommendRequest;
use App\Models\Vehicle;
use App\Services\Application\Movement\MovementRecommendService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MovementRecommendController extends ApiController
{
    /**
     * @var MovementRecommendService
     */
    private $movementRecommendService;

    public function __construct(
        MovementRecommendService $movementRecommendService
    ) {
        $this->middleware('role:Super-Admin|admin');
        $this->movementRecommendService = $movementRecommendService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/movement/recomend",
     *      tags={"Movements"},
     *      summary="Movements Recommend",
     *      description="Este servicio contempla las cuatro acciones definidas para mover un vehículo desde que sale de CANOPY
     *                   Las acciones son las siguientes:
     *                   - action = 1 : Se ejecuta al pulsar el botón OK y hace una recomendación de movimiento en presorting
     *                   - action = 2 : Se ejecuta al pulsar el botón NOK y muestra desplegable para mandar vehículo a planta
     *                   - action = 3 : Se ejecuta al pulsar el botón VENTAS y permite elegir ubicación manual
     *                   - action = 4 : Se ejecuta al pulsar el botón ESCAPE y hace una recomendación de movimiento en posición final",
     *      security={{"sanctum": {}}},
     *      operationId="recommendMovements",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(ref="#/components/schemas/MovementRecommendRequest")
     *      ),
     *      @OA\Response(response=200, description="Movement Recommend Successfully"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param MovementRecommendRequest $request
     * @return JsonResponse
     */
    public function index(MovementRecommendRequest $request): JsonResponse
    {
        $response = $this->movementRecommendService->movement($request->all());
        return $this->successResponse($response);
    }

}
