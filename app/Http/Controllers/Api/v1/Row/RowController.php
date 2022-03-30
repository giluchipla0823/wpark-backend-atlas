<?php

namespace App\Http\Controllers\Api\v1\Row;

use App\Models\Row;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Row\RowService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Row\RowStoreRequest;
use App\Http\Requests\Row\RowUpdateRequest;

class RowController extends ApiController
{
    /**
     * @var RowService
     */
    private $rowService;

    public function __construct(
        RowService $rowService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->rowService = $rowService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/rows",
     *      tags={"Rows"},
     *      summary="Rows List",
     *      description="List of rows",
     *      security={{"sanctum": {}}},
     *      operationId="indexRows",
     *      @OA\Response(response=200, description="Row list Successfully"),
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
        $results = $this->rowService->all($request);

        return $this->showAll($results);
    }

    /**
     * @param RowStoreRequest $request
     * @return JsonResponse
     */
    /* public function store(RowStoreRequest $request): JsonResponse
    {
        $row = $this->rowService->create($request->all());

        return $this->successResponse($row, 'Row created successfully.', Response::HTTP_CREATED);
    } */

    /**
     * @OA\GET(
     *     path="/api/v1/rows/{id}",
     *     tags={"Rows"},
     *     summary="Show Row Details",
     *     description="Show Row Details",
     *     security={{"sanctum": {}}},
     *     operationId="showRow",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Row Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Row $row
     * @return JsonResponse
     */
    public function show(Row $row): JsonResponse
    {
        $row = $this->rowService->show($row);
        return $this->successResponse($row);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/rows/{id}",
     *     tags={"Rows"},
     *     summary="Update Row",
     *     description="Update Row",
     *     security={{"sanctum": {}}},
     *     operationId="updateRow",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RowUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Row" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param RowUpdateRequest $request
     * @param Row $row
     * @return JsonResponse
     */
    public function update(RowUpdateRequest $request, Row $row): JsonResponse
    {
        $this->rowService->update($request->all(), $row->id);

        return $this->showMessage('Row updated successfully.');
    }

    /**
     * @param Row $row
     * @return JsonResponse
     */
    /* public function destroy(Row $row): JsonResponse
    {
        $this->rowService->delete($row->id);

        return $this->showMessage('Row removed successfully.', Response::HTTP_NO_CONTENT);
    } */

    /**
     * @param int $id
     * @return JsonResponse
     */
    /* public function restore(int $id): JsonResponse
    {
        $this->rowService->restore($id);

        return $this->showMessage('Row restored successfully.', Response::HTTP_NO_CONTENT);
    } */


}
