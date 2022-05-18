<?php

namespace App\Http\Controllers\Api\v1\Dealer;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Dealer\DealerStoreRequest;
use App\Http\Requests\Dealer\DealerUpdateRequest;
use App\Models\Dealer;
use App\Services\Application\Dealer\DealerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DealerController extends ApiController
{
    /**
     * @var DealerService
     */
    private $dealerService;

    public function __construct(
        DealerService $dealerService
    ){
        $this->middleware('role:Super-Admin|admin');
        $this->dealerService = $dealerService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/dealers",
     *      tags={"Dealers"},
     *      summary="Dealers List",
     *      description="List of dealers",
     *      security={{"sanctum": {}}},
     *      operationId="indexDealers",
     *      @OA\Response(response=200, description="Dealers list Successfully"),
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
        $results = $this->dealerService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/dealers",
     *     tags={"Dealers"},
     *     summary="Create New Dealer",
     *     description="Create New Dealer",
     *     security={{"sanctum": {} }},
     *     operationId="storeDealer",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DealerStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Dealer" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param DealerStoreRequest $request
     * @return JsonResponse
     */
    public function store(DealerStoreRequest $request): JsonResponse
    {
        $dealer = $this->dealerService->create($request->all());

        return $this->successResponse($dealer, 'Dealer created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/dealers/{id}",
     *     tags={"Dealers"},
     *     summary="Show Dealer Details",
     *     description="Show Dealer Details",
     *     security={{"sanctum": {}}},
     *     operationId="showDealer",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Dealer Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Dealer $dealer
     * @return JsonResponse
     */
    public function show(Dealer $dealer): JsonResponse
    {
        $dealer = $this->dealerService->show($dealer);

        return $this->successResponse($dealer);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/dealers/{id}",
     *     tags={"Dealers"},
     *     summary="Update Dealer",
     *     description="Update Dealer",
     *     security={{"sanctum": {}}},
     *     operationId="updateDealer",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DealerUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Dealer" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param DealerUpdateRequest $request
     * @param Dealer $dealer
     * @return JsonResponse
     */
    public function update(DealerUpdateRequest $request, Dealer $dealer): JsonResponse
    {
        $this->dealerService->update($request->all(), $dealer->id);

        return $this->showMessage('Dealer updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/dealers/{id}",
     *     tags={"Dealers"},
     *     summary="Delete Dealer",
     *     description="Delete Dealer",
     *     security={{"sanctum": {}}},
     *     operationId="destroyDealer",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Dealer successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Dealer $dealer
     * @return JsonResponse
     */
    public function destroy(Dealer $dealer): JsonResponse
    {
        $this->dealerService->delete($dealer->id);

        return $this->showMessage('Dealer removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/dealers/{id}",
     *     tags={"Dealers"},
     *     summary="Restore Dealer",
     *     description="Restore Dealer",
     *     security={{"sanctum": {}}},
     *     operationId="restoreDealer",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Dealer restored successfully"),
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
        $this->dealerService->restore($id);

        return $this->showMessage('Dealer restored successfully.');
    }
}
