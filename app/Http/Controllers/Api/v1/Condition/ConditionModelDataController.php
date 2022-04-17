<?php

namespace App\Http\Controllers\Api\v1\Condition;

use Exception;
use App\Models\Condition;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Services\Condition\ConditionService;

class ConditionModelDataController extends ApiController
{
    /**
     * @var ConditionService
     */
    private $conditionService;

    public function __construct(
        ConditionService $conditionService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->conditionService = $conditionService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/conditions/{id}/model-data",
     *      tags={"Conditions"},
     *      summary="Model data list of condition",
     *      description="Model data list of condition",
     *      security={{"sanctum": {}}},
     *      operationId="indexConditionsModelData",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Model data list of Condition Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Condition $condition
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Condition $condition): JsonResponse
    {
        $results = $this->conditionService->getModelDataByCondition($condition);

        return $this->successResponse($results);
    }
}
