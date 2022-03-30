<?php

namespace App\Http\Controllers\Api\v1\Slot;

use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Slot\SlotService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Slot\SlotStoreRequest;
use App\Http\Requests\Slot\SlotUpdateRequest;

class SlotController extends ApiController
{
    /**
     * @var SlotService
     */
    private $slotService;

    public function __construct(
        SlotService $slotService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->slotService = $slotService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/slots",
     *      tags={"Slots"},
     *      summary="Slots List",
     *      description="List of slots",
     *      security={{"sanctum": {}}},
     *      operationId="indexSlots",
     *      @OA\Response(response=200, description="Slot list Successfully"),
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
        $results = $this->slotService->all($request);

        return $this->showAll($results);
    }

    /**
     * @param SlotStoreRequest $request
     * @return JsonResponse
     */
    /* public function store(SlotStoreRequest $request): JsonResponse
    {
        $slot = $this->slotService->create($request->all());

        return $this->successResponse($slot, 'Slot created successfully.', Response::HTTP_CREATED);
    } */

    /**
     * @OA\GET(
     *     path="/api/v1/slots/{id}",
     *     tags={"Slots"},
     *     summary="Show Slot Details",
     *     description="Show Slot Details",
     *     security={{"sanctum": {}}},
     *     operationId="showSlot",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Slot Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Slot $slot
     * @return JsonResponse
     */
    public function show(Slot $slot): JsonResponse
    {
        $slot = $this->slotService->show($slot);
        return $this->successResponse($slot);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/slots/{id}",
     *     tags={"Slots"},
     *     summary="Update Slot",
     *     description="Update Slot",
     *     security={{"sanctum": {}}},
     *     operationId="updateSlot",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/SlotUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Slot" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param SlotUpdateRequest $request
     * @param Slot $slot
     * @return JsonResponse
     */
    public function update(SlotUpdateRequest $request, Slot $slot): JsonResponse
    {
        $this->slotService->update($request->all(), $slot->id);

        return $this->showMessage('Slot updated successfully.');
    }

    /**
     * @param Slot $slot
     * @return JsonResponse
     */
    /* public function destroy(Slot $slot): JsonResponse
    {
        $this->slotService->delete($slot->id);

        return $this->showMessage('Slot removed successfully.', Response::HTTP_NO_CONTENT);
    } */

    /**
     * @param int $id
     * @return JsonResponse
     */
    /* public function restore(int $id): JsonResponse
    {
        $this->slotService->restore($id);

        return $this->showMessage('Slot restored successfully.', Response::HTTP_NO_CONTENT);
    } */


}
