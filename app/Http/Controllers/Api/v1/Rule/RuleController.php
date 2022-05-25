<?php

namespace App\Http\Controllers\Api\v1\Rule;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Rule\RuleStoreRequest;
use App\Http\Requests\Rule\RuleUpdateRequest;
use App\Models\Rule;
use App\Services\Application\Rule\RuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RuleController extends ApiController
{
    /**
     * @var RuleService
     */
    private $ruleService;

    public function __construct(
        RuleService $ruleService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->ruleService = $ruleService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/rules",
     *      tags={"Rules"},
     *      summary="Rules List",
     *      description="List of rules",
     *      security={{"sanctum": {}}},
     *      operationId="indexRules",
     *      @OA\Parameter(ref="#/components/parameters/datatables"),
     *      @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Añadir bloques, condiciones",
     *         example="blocks,conditions",
     *         required=false
     *      ),
     *      @OA\Parameter(
     *         name="extra_includes",
     *         in="query",
     *         description="Añadir valores de condiciones",
     *         example="conditions.values",
     *         required=false
     *      ),
     *      @OA\Response(response=200, description="Rule list Successfully"),
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
        $results = $this->ruleService->all($request);

        return $this->showAll($results);
    }

    public function datatables(Request $request): JsonResponse
    {
        $results = $this->ruleService->datatables($request);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/rules",
     *     tags={"Rules"},
     *     summary="Create New Rule",
     *     description="Create New Rule",
     *     security={{"sanctum": {} }},
     *     operationId="storeRule",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RuleStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Rule" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param RuleStoreRequest $request
     * @return JsonResponse
     */
    public function store(RuleStoreRequest $request): JsonResponse
    {
        $rule = $this->ruleService->create($request->all());

        return $this->successResponse($rule, 'Rule created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/rules/{id}",
     *     tags={"Rules"},
     *     summary="Show Rule Details",
     *     description="Show Rule Details",
     *     security={{"sanctum": {}}},
     *     operationId="showRule",
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
     *     @OA\Response(response=200, description="Show Rule Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Rule $rule
     * @return JsonResponse
     */
    public function show(Rule $rule): JsonResponse
    {
        $rule = $this->ruleService->show($rule);
        return $this->successResponse($rule);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/rules/{id}",
     *     tags={"Rules"},
     *     summary="Update Rule",
     *     description="Update Rule",
     *     security={{"sanctum": {}}},
     *     operationId="updateRule",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RuleUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Rule" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param RuleUpdateRequest $request
     * @param Rule $rule
     * @return JsonResponse
     */
    public function update(RuleUpdateRequest $request, Rule $rule): JsonResponse
    {
        $this->ruleService->update($request->all(), $rule->id);

        return $this->showMessage('Rule updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/rules/{id}",
     *     tags={"Rules"},
     *     summary="Delete Rule",
     *     description="Delete Rule",
     *     security={{"sanctum": {}}},
     *     operationId="destroyRule",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Rule successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Rule $rule
     * @return JsonResponse
     */
    public function destroy(Rule $rule): JsonResponse
    {
        $this->ruleService->delete($rule->id);

        return $this->showMessage('Rule removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/rules/{id}",
     *     tags={"Rules"},
     *     summary="Restore Rule",
     *     description="Restore Rule",
     *     security={{"sanctum": {}}},
     *     operationId="restoreRule",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Rule restored successfully"),
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
        $this->ruleService->restore($id);

        return $this->showMessage('Rule restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/rules/{id}/toggle-active",
     *     tags={"Rules"},
     *     summary="Toggle Active Rule",
     *     description="Toggle Active Rule",
     *     security={{"sanctum": {}}},
     *     operationId="toggleActiveRule",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Rule toggle active successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Toggle active the specified resource from storage.
     *
     * @param Rule $rule
     * @return JsonResponse
     */
    public function toggleActive(Rule $rule): JsonResponse
    {
        $active = $this->ruleService->toggleActive($rule);

        $message = $active === 0 ? 'La regla se desactivó correctamente' : 'La regla se activó correctamente';

        return $this->showMessage($message);
    }


}
