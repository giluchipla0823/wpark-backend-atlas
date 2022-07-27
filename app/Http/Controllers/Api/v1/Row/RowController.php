<?php

namespace App\Http\Controllers\Api\v1\Row;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Row\RowUpdateRequest;
use App\Models\Row;
use App\Services\Application\Row\RowService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $results = $this->rowService->all($request);

        return $this->showAll($results);
    }

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
     * @OA\PATCH(
     *     path="/api/v1/rows/{id}/toggle-active",
     *     tags={"Rows"},
     *     summary="Toggle Active Row",
     *     description="Toggle Active Row",
     *     security={{"sanctum": {}}},
     *     operationId="toggleActiveRow",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          @OA\JsonContent(ref="#/components/schemas/RowToggleActiveRequest")
     *     ),
     *     @OA\Response(response=200, description="Row toggle active successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Toggle active the specified resource from storage.
     *
     * @param Row $row
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleActive(Row $row, Request $request): JsonResponse
    {
        $active = $this->rowService->toggleActive($row, $request->get('comments'));

        $message = $active === 0 ? 'La fila se desactivó correctamente.' : 'La fila se activó correctamente.';

        return $this->showMessage($message);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/rows/show-by-qrcode/{qrcode}",
     *     tags={"Rows"},
     *     summary="Show Row Details by QR code",
     *     description="Show Row Details by QR code",
     *     security={{"sanctum": {}}},
     *     operationId="showRowByQrcode",
     *     @OA\Parameter(
     *          parameter="qrcode",
     *          name="qrcode",
     *          description="qrcode, eg; 100.001",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Show Row Details by QR code"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource by QR code.
     *
     * @param string $qrcode
     * @return JsonResponse
     * @throws Exception
     */
    public function showByQrCode(string $qrcode): JsonResponse
    {
        $row = $this->rowService->findOneByQrcode($qrcode);

        return $this->successResponse($row);
    }

}
