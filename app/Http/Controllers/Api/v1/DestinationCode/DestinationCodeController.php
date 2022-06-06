<?php

namespace App\Http\Controllers\Api\v1\DestinationCode;

use App\Http\Controllers\ApiController;
use App\Http\Requests\DestinationCode\DestinationCodeStoreRequest;
use App\Http\Requests\DestinationCode\DestinationCodeUpdateRequest;
use App\Models\DestinationCode;
use App\Services\Application\DestinationCode\DestinationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DestinationCodeController extends ApiController
{
    /**
     * @var DestinationCodeService
     */
    private $destinationCodeService;

    public function __construct(
        DestinationCodeService $destinationCodeService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->destinationCodeService = $destinationCodeService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/destination-codes",
     *      tags={"DestinationCodes"},
     *      summary="DestinationCodes List",
     *      description="List of destinationCodes",
     *      security={{"sanctum": {}}},
     *      @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         description="Filtro por id de país",
     *         example="1",
     *         required=false
     *      ),
     *      operationId="indexDestinationCodes",
     *      @OA\Response(response=200, description="DestinationCode list Successfully"),
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
        $results = $this->destinationCodeService->all($request);

        return $this->showAll($results);
    }


    /**
     * @OA\Get(
     *      path="/api/v1/destination-codes/datatables",
     *      tags={"DestinationCodes"},
     *      summary="DestinationCodes List with datatables",
     *      description="List of destinationCodes with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesDestinationCodes",
     *      @OA\Response(response=200, description="DestinationCode list with datatables Successfully"),
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
        $results = $this->destinationCodeService->datatables($request);

        return $this->datatablesResponse($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/destination-codes",
     *     tags={"DestinationCodes"},
     *     summary="Create New DestinationCode",
     *     description="Create New DestinationCode",
     *     security={{"sanctum": {} }},
     *     operationId="storeDestinationCode",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DestinationCodeStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New DestinationCode" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param DestinationCodeStoreRequest $request
     * @return JsonResponse
     */
    public function store(DestinationCodeStoreRequest $request): JsonResponse
    {
        $destinationCode = $this->destinationCodeService->create($request->all());

        return $this->successResponse($destinationCode, 'DestinationCode created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/destination-codes/{id}",
     *     tags={"DestinationCodes"},
     *     summary="Show DestinationCode Details",
     *     description="Show DestinationCode Details",
     *     security={{"sanctum": {}}},
     *     operationId="showDestinationCode",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show DestinationCode Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param DestinationCode $destinationCode
     * @return JsonResponse
     */
    public function show(DestinationCode $destinationCode): JsonResponse
    {
        $destinationCode = $this->destinationCodeService->show($destinationCode);
        return $this->successResponse($destinationCode);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/destination-codes/{id}",
     *     tags={"DestinationCodes"},
     *     summary="Update DestinationCode",
     *     description="Update DestinationCode",
     *     security={{"sanctum": {}}},
     *     operationId="updateDestinationCode",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DestinationCodeUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update DestinationCode" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param DestinationCodeUpdateRequest $request
     * @param DestinationCode $destinationCode
     * @return JsonResponse
     */
    public function update(DestinationCodeUpdateRequest $request, DestinationCode $destinationCode): JsonResponse
    {
        $this->destinationCodeService->update($request->all(), $destinationCode->id);

        return $this->showMessage('DestinationCode updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/destination-codes/{id}",
     *     tags={"DestinationCodes"},
     *     summary="Delete DestinationCode",
     *     description="Delete DestinationCode",
     *     security={{"sanctum": {}}},
     *     operationId="destroyDestinationCode",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete DestinationCode successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param DestinationCode $destinationCode
     * @return JsonResponse
     */
    public function destroy(DestinationCode $destinationCode): JsonResponse
    {
        $this->destinationCodeService->delete($destinationCode->id);

        return $this->showMessage('DestinationCode removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/destination-codes/{id}",
     *     tags={"DestinationCodes"},
     *     summary="Restore DestinationCode",
     *     description="Restore DestinationCode",
     *     security={{"sanctum": {}}},
     *     operationId="restoreDestinationCode",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="DestinationCode restored successfully"),
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
        $this->destinationCodeService->restore($id);

        return $this->showMessage('DestinationCode restored successfully.');
    }


}
