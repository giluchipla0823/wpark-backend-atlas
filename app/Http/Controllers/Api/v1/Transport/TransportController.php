<?php

namespace App\Http\Controllers\Api\v1\Transport;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Transport\TransportStoreRequest;
use App\Http\Requests\Transport\TransportUpdateRequest;
use App\Models\Transport;
use App\Services\Application\Transport\TransportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransportController extends ApiController
{
    /**
     * @var TransportService
     */
    private $transportService;

    public function __construct(
        TransportService $transportService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->transportService = $transportService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/transports",
     *      tags={"Transports"},
     *      summary="Transports List",
     *      description="List of transports",
     *      security={{"sanctum": {}}},
     *      operationId="indexTransports",
     *      @OA\Response(response=200, description="Transport list Successfully"),
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
        $results = $this->transportService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/transports",
     *     tags={"Transports"},
     *     summary="Create New Transport",
     *     description="Create New Transport",
     *     security={{"sanctum": {} }},
     *     operationId="storeTransport",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TransportStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Transport" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param TransportStoreRequest $request
     * @return JsonResponse
     */
    public function store(TransportStoreRequest $request): JsonResponse
    {
        $transport = $this->transportService->create($request->all());

        return $this->successResponse($transport, 'Transport created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/transports/{id}",
     *     tags={"Transports"},
     *     summary="Show Transport Details",
     *     description="Show Transport Details",
     *     security={{"sanctum": {}}},
     *     operationId="showTransport",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Transport Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Transport $transport
     * @return JsonResponse
     */
    public function show(Transport $transport): JsonResponse
    {
        $transport = $this->transportService->show($transport);
        return $this->successResponse($transport);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/transports/{id}",
     *     tags={"Transports"},
     *     summary="Update Transport",
     *     description="Update Transport",
     *     security={{"sanctum": {}}},
     *     operationId="updateTransport",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TransportUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Transport" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param TransportUpdateRequest $request
     * @param Transport $transport
     * @return JsonResponse
     */
    public function update(TransportUpdateRequest $request, Transport $transport): JsonResponse
    {
        $this->transportService->update($request->all(), $transport->id);

        return $this->showMessage('Transport updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/transports/{id}",
     *     tags={"Transports"},
     *     summary="Delete Transport",
     *     description="Delete Transport",
     *     security={{"sanctum": {}}},
     *     operationId="destroyTransport",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Transport successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Transport $transport
     * @return JsonResponse
     */
    public function destroy(Transport $transport): JsonResponse
    {
        $this->transportService->delete($transport->id);

        return $this->showMessage('Transport removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/transports/{id}",
     *     tags={"Transports"},
     *     summary="Restore Transport",
     *     description="Restore Transport",
     *     security={{"sanctum": {}}},
     *     operationId="restoreTransport",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Transport restored successfully"),
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
        $this->transportService->restore($id);

        return $this->showMessage('Transport restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/transports/{id}/toggle-active",
     *     tags={"Transports"},
     *     summary="Toggle Active Transport",
     *     description="Toggle Active Transport",
     *     security={{"sanctum": {}}},
     *     operationId="toggleActiveTransport",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Transport toggle active successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Toggle active the specified resource from storage.
     *
     * @param Transport $transport
     * @return JsonResponse
     */
    public function toggleActive(Transport $transport): JsonResponse
    {
        $active = $this->transportService->toggleActive($transport);

        $message = $active === 0 ? 'El transporte se desactivó correctamente.' : 'El transporte se activó correctamente.';

        return $this->showMessage($message);
    }


}
