<?php

namespace App\Http\Controllers\Api\v1\Compound;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Compound\CompoundStoreRequest;
use App\Http\Requests\Compound\CompoundUpdateRequest;
use App\Models\Compound;
use App\Services\Application\Compound\CompoundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompoundController extends ApiController
{
    /**
     * @var CompoundService
     */
    private $compoundService;

    public function __construct(
        CompoundService $compoundService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->compoundService = $compoundService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/compounds",
     *      tags={"Compounds"},
     *      summary="Compounds List",
     *      description="List of compounds",
     *      security={{"sanctum": {}}},
     *      operationId="indexCompounds",
     *      @OA\Response(response=200, description="Compound list Successfully"),
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
        $results = $this->compoundService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/compounds/datatables",
     *      tags={"Compounds"},
     *      summary="Compounds List with datatables",
     *      description="List of compounds with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesCompounds",
     *      @OA\Response(response=200, description="Compound list with datatables Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource with datatables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatables(Request $request): JsonResponse
    {
        $results = $this->compoundService->datatables($request);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/compounds",
     *     tags={"Compounds"},
     *     summary="Create New Compound",
     *     description="Create New Compound",
     *     security={{"sanctum": {} }},
     *     operationId="storeCompound",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CompoundStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Compound" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param CompoundStoreRequest $request
     * @return JsonResponse
     */
    public function store(CompoundStoreRequest $request): JsonResponse
    {
        $compound = $this->compoundService->create($request->all());

        return $this->successResponse($compound, 'Compound created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/compounds/{id}",
     *     tags={"Compounds"},
     *     summary="Show Compound Details",
     *     description="Show Compound Details",
     *     security={{"sanctum": {}}},
     *     operationId="showCompound",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Compound Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Compound $compound
     * @return JsonResponse
     */
    public function show(Compound $compound): JsonResponse
    {
        $compound = $this->compoundService->show($compound);
        return $this->successResponse($compound);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/compounds/{id}",
     *     tags={"Compounds"},
     *     summary="Update Compound",
     *     description="Update Compound",
     *     security={{"sanctum": {}}},
     *     operationId="updateCompound",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CompoundUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Compound" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param CompoundUpdateRequest $request
     * @param Compound $compound
     * @return JsonResponse
     */
    public function update(CompoundUpdateRequest $request, Compound $compound): JsonResponse
    {
        $this->compoundService->update($request->all(), $compound->id);

        return $this->showMessage('Compound updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/compounds/{id}",
     *     tags={"Compounds"},
     *     summary="Delete Compound",
     *     description="Delete Compound",
     *     security={{"sanctum": {}}},
     *     operationId="destroyCompound",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Compound successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Compound $compound
     * @return JsonResponse
     */
    public function destroy(Compound $compound): JsonResponse
    {
        $this->compoundService->delete($compound->id);

        return $this->showMessage('Compound removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/compounds/{id}",
     *     tags={"Compounds"},
     *     summary="Restore Compound",
     *     description="Restore Compound",
     *     security={{"sanctum": {}}},
     *     operationId="restoreCompound",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Compound restored successfully"),
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
        $this->compoundService->restore($id);

        return $this->showMessage('Compound restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
