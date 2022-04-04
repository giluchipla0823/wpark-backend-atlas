<?php

namespace App\Http\Controllers\Api\v1\Condition;

use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Condition\ConditionService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Condition\ConditionStoreRequest;
use App\Http\Requests\Condition\ConditionUpdateRequest;

class ConditionController extends ApiController
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
     *      path="/api/v1/conditions",
     *      tags={"Conditions"},
     *      summary="Conditions List",
     *      description="List of conditions",
     *      security={{"sanctum": {}}},
     *      operationId="indexConditions",
     *      @OA\Response(response=200, description="Condition list Successfully"),
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
        $results = $this->conditionService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/conditions",
     *     tags={"Conditions"},
     *     summary="Create New Condition",
     *     description="Create New Condition",
     *     security={{"sanctum": {} }},
     *     operationId="storeCondition",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ConditionStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Condition" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ConditionStoreRequest $request
     * @return JsonResponse
     */
    public function store(ConditionStoreRequest $request): JsonResponse
    {
        $condition = $this->conditionService->create($request->all());

        return $this->successResponse($condition, 'Condition created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/conditions/{id}",
     *     tags={"Conditions"},
     *     summary="Show Condition Details",
     *     description="Show Condition Details",
     *     security={{"sanctum": {}}},
     *     operationId="showCondition",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Condition Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Condition $condition
     * @return JsonResponse
     */
    public function show(Condition $condition): JsonResponse
    {
        $condition = $this->conditionService->show($condition);
        return $this->successResponse($condition);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/conditions/{id}",
     *     tags={"Conditions"},
     *     summary="Update Condition",
     *     description="Update Condition",
     *     security={{"sanctum": {}}},
     *     operationId="updateCondition",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ConditionUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Condition" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ConditionUpdateRequest $request
     * @param Condition $condition
     * @return JsonResponse
     */
    public function update(ConditionUpdateRequest $request, Condition $condition): JsonResponse
    {
        $this->conditionService->update($request->all(), $condition->id);

        return $this->showMessage('Condition updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/conditions/{id}",
     *     tags={"Conditions"},
     *     summary="Delete Condition",
     *     description="Delete Condition",
     *     security={{"sanctum": {}}},
     *     operationId="destroyCondition",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Condition successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Condition $condition
     * @return JsonResponse
     */
    public function destroy(Condition $condition): JsonResponse
    {
        $this->conditionService->delete($condition->id);

        return $this->showMessage('Condition removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/conditions/{id}",
     *     tags={"Conditions"},
     *     summary="Restore Condition",
     *     description="Restore Condition",
     *     security={{"sanctum": {}}},
     *     operationId="restoreCondition",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Condition restored successfully"),
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
        $this->conditionService->restore($id);

        return $this->showMessage('Condition restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
