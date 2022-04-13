<?php

namespace App\Http\Controllers\Api\v1\Carrier;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Carrier\CarrierStoreRequest;
use App\Http\Requests\Carrier\CarrierUpdateRequest;
use App\Models\Carrier;
use App\Services\Carrier\CarrierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarrierController extends ApiController
{
    /**
     * @var CarrierService
     */
    private $carrierService;

    public function __construct(
        CarrierService $carrierService
    ){
        $this->middleware('role:Super-Admin|admin');
        $this->carrierService = $carrierService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/carriers",
     *      tags={"Carriers"},
     *      summary="Carriers List",
     *      description="List of carriers",
     *      security={{"sanctum": {}}},
     *      operationId="indexCarriers",
     *      @OA\Response(response=200, description="Carriers list Successfully"),
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
        $results = $this->carrierService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/carriers",
     *     tags={"Carriers"},
     *     summary="Create New Carrier",
     *     description="Create New Carrier",
     *     security={{"sanctum": {} }},
     *     operationId="storeCarrier",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CarrierStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Carrier" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param CarrierStoreRequest $request
     * @return JsonResponse
     */
    public function store(CarrierStoreRequest $request): JsonResponse
    {
        $carrier = $this->carrierService->create($request->all());

        return $this->successResponse($carrier, 'Carrier created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/carriers/{id}",
     *     tags={"Carriers"},
     *     summary="Show Carrier Details",
     *     description="Show Carrier Details",
     *     security={{"sanctum": {}}},
     *     operationId="showCarrier",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Carrier Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Carrier $carrier
     * @return JsonResponse
     */
    public function show(Carrier $carrier): JsonResponse
    {
        $carrier = $this->carrierService->show($carrier);

        return $this->successResponse($carrier);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/carriers/{id}",
     *     tags={"Carriers"},
     *     summary="Update Carrier",
     *     description="Update Carrier",
     *     security={{"sanctum": {}}},
     *     operationId="updateCarrier",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CarrierUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Carrier" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param CarrierUpdateRequest $request
     * @param Carrier $carrier
     * @return JsonResponse
     */
    public function update(CarrierUpdateRequest $request, Carrier $carrier): JsonResponse
    {
        $this->carrierService->update($request->all(), $carrier->id);

        return $this->showMessage('Carrier updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/carriers/{id}",
     *     tags={"Carriers"},
     *     summary="Delete Carrier",
     *     description="Delete Carrier",
     *     security={{"sanctum": {}}},
     *     operationId="destroyCarrier",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Carrier successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Carrier $carrier
     * @return JsonResponse
     */
    public function destroy(Carrier $carrier): JsonResponse
    {
        $this->carrierService->delete($carrier->id);

        return $this->showMessage('Carrier removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/carriers/{id}",
     *     tags={"Carriers"},
     *     summary="Restore Carrier",
     *     description="Restore Carrier",
     *     security={{"sanctum": {}}},
     *     operationId="restoreCarrier",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Carrier restored successfully"),
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
        $this->carrierService->restore($id);

        return $this->showMessage('Carrier restored successfully.');
    }
}
