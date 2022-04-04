<?php

namespace App\Http\Controllers\Api\v1\Vehicle;

use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Vehicle\StageService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Vehicle\StageStoreRequest;
use App\Http\Requests\Vehicle\StageUpdateRequest;

class StageController extends ApiController
{
    /**
     * @var StageService
     */
    private $stageService;

    public function __construct(
        StageService $stageService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->stageService = $stageService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/stages",
     *      tags={"Stages"},
     *      summary="Stages List",
     *      description="List of stages",
     *      security={{"sanctum": {}}},
     *      operationId="indexStages",
     *      @OA\Response(response=200, description="Stage list Successfully"),
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
        $results = $this->stageService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/stages",
     *     tags={"Stages"},
     *     summary="Create New Stage",
     *     description="Create New Stage",
     *     security={{"sanctum": {} }},
     *     operationId="storeStage",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StageStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Stage" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param StageStoreRequest $request
     * @return JsonResponse
     */
    public function store(StageStoreRequest $request): JsonResponse
    {
        $stage = $this->stageService->create($request->all());

        return $this->successResponse($stage, 'Stage created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stages/{id}",
     *     tags={"Stages"},
     *     summary="Show Stage Details",
     *     description="Show Stage Details",
     *     security={{"sanctum": {}}},
     *     operationId="showStage",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Stage Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Stage $stage
     * @return JsonResponse
     */
    public function show(Stage $stage): JsonResponse
    {
        $stage = $this->stageService->show($stage);
        return $this->successResponse($stage);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/stages/{id}",
     *     tags={"Stages"},
     *     summary="Update Stage",
     *     description="Update Stage",
     *     security={{"sanctum": {}}},
     *     operationId="updateStage",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StageUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Stage" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param StageUpdateRequest $request
     * @param Stage $stage
     * @return JsonResponse
     */
    public function update(StageUpdateRequest $request, Stage $stage): JsonResponse
    {
        $this->stageService->update($request->all(), $stage->id);

        return $this->showMessage('Stage updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/stages/{id}",
     *     tags={"Stages"},
     *     summary="Delete Stage",
     *     description="Delete Stage",
     *     security={{"sanctum": {}}},
     *     operationId="destroyStage",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Stage successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Stage $stage
     * @return JsonResponse
     */
    public function destroy(Stage $stage): JsonResponse
    {
        $this->stageService->delete($stage->id);

        return $this->showMessage('Stage removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/stages/{id}",
     *     tags={"Stages"},
     *     summary="Restore Stage",
     *     description="Restore Stage",
     *     security={{"sanctum": {}}},
     *     operationId="restoreStage",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Stage restored successfully"),
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
        $this->stageService->restore($id);

        return $this->showMessage('Stage restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
