<?php

namespace App\Http\Controllers\Api\v1\State;

use App\Http\Controllers\ApiController;
use App\Http\Requests\State\StateStoreRequest;
use App\Http\Requests\State\StateUpdateRequest;
use App\Models\State;
use App\Services\Application\State\StateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StateController extends ApiController
{
    /**
     * @var StateService
     */
    private $stateService;

    public function __construct(
        StateService $stateService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->stateService = $stateService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/states",
     *      tags={"States"},
     *      summary="States List",
     *      description="List of states",
     *      security={{"sanctum": {}}},
     *      operationId="indexStates",
     *      @OA\Response(response=200, description="State list Successfully"),
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
        $results = $this->stateService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/states",
     *     tags={"States"},
     *     summary="Create New State",
     *     description="Create New State",
     *     security={{"sanctum": {} }},
     *     operationId="storeState",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StateStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New State" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param StateStoreRequest $request
     * @return JsonResponse
     */
    public function store(StateStoreRequest $request): JsonResponse
    {
        $state = $this->stateService->create($request->all());

        return $this->successResponse($state, 'State created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/states/{id}",
     *     tags={"States"},
     *     summary="Show State Details",
     *     description="Show State Details",
     *     security={{"sanctum": {}}},
     *     operationId="showState",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show State Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param State $state
     * @return JsonResponse
     */
    public function show(State $state): JsonResponse
    {
        $state = $this->stateService->show($state);
        return $this->successResponse($state);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/states/{id}",
     *     tags={"States"},
     *     summary="Update State",
     *     description="Update State",
     *     security={{"sanctum": {}}},
     *     operationId="updateState",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StateUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update State" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param StateUpdateRequest $request
     * @param State $state
     * @return JsonResponse
     */
    public function update(StateUpdateRequest $request, State $state): JsonResponse
    {
        $this->stateService->update($request->all(), $state->id);

        return $this->showMessage('State updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/states/{id}",
     *     tags={"States"},
     *     summary="Delete State",
     *     description="Delete State",
     *     security={{"sanctum": {}}},
     *     operationId="destroyState",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete State successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param State $state
     * @return JsonResponse
     */
    public function destroy(State $state): JsonResponse
    {
        $this->stateService->delete($state->id);

        return $this->showMessage('State removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/states/{id}",
     *     tags={"States"},
     *     summary="Restore State",
     *     description="Restore State",
     *     security={{"sanctum": {}}},
     *     operationId="restoreState",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="State restored successfully"),
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
        $this->stateService->restore($id);

        return $this->showMessage('State restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
