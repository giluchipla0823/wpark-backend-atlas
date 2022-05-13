<?php

namespace App\Http\Controllers\Api\v1\Design;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Design\DesignStoreRequest;
use App\Http\Requests\Design\DesignUpdateRequest;
use App\Models\Design;
use App\Services\Application\Design\DesignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DesignController extends ApiController
{
    /**
     * @var DesignService
     */
    private $designService;

    public function __construct(
        DesignService $designService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->designService = $designService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/designs",
     *      tags={"Designs"},
     *      summary="Designs List",
     *      description="List of designs",
     *      security={{"sanctum": {}}},
     *      operationId="indexDesigns",
     *      @OA\Response(response=200, description="Design list Successfully"),
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
        $results = $this->designService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/designs",
     *     tags={"Designs"},
     *     summary="Create New Design",
     *     description="Create New Design",
     *     security={{"sanctum": {} }},
     *     operationId="storeDesign",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DesignStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Design" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param DesignStoreRequest $request
     * @return JsonResponse
     */
    public function store(DesignStoreRequest $request): JsonResponse
    {
        $design = $this->designService->create($request->all());

        return $this->successResponse($design, 'Design created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/designs/{id}",
     *     tags={"Designs"},
     *     summary="Show Design Details",
     *     description="Show Design Details",
     *     security={{"sanctum": {}}},
     *     operationId="showDesign",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Design Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Design $design
     * @return JsonResponse
     */
    public function show(Design $design): JsonResponse
    {
        $design = $this->designService->show($design);
        return $this->successResponse($design);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/designs/{id}",
     *     tags={"Designs"},
     *     summary="Update Design",
     *     description="Update Design",
     *     security={{"sanctum": {}}},
     *     operationId="updateDesign",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DesignUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Design" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param DesignUpdateRequest $request
     * @param Design $design
     * @return JsonResponse
     */
    public function update(DesignUpdateRequest $request, Design $design): JsonResponse
    {
        $this->designService->update($request->all(), $design->id);

        return $this->showMessage('Design updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/designs/{id}",
     *     tags={"Designs"},
     *     summary="Delete Design",
     *     description="Delete Design",
     *     security={{"sanctum": {}}},
     *     operationId="destroyDesign",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Design successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Design $design
     * @return JsonResponse
     */
    public function destroy(Design $design): JsonResponse
    {
        $this->designService->delete($design->id);

        return $this->showMessage('Design removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/designs/{id}",
     *     tags={"Designs"},
     *     summary="Restore Design",
     *     description="Restore Design",
     *     security={{"sanctum": {}}},
     *     operationId="restoreDesign",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Design restored successfully"),
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
        $this->designService->restore($id);

        return $this->showMessage('Design restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
