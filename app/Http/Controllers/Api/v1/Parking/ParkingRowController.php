<?php

namespace App\Http\Controllers\Api\v1\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking;
use App\Services\Row\RowService;
use Illuminate\Http\JsonResponse;

class ParkingRowController extends ApiController
{
    /**
     * @var RowService
     */
    private $rowService;

    public function __construct(
        RowService $rowService
    ) {
        $this->rowService = $rowService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/parkings/{id}/rows",
     *      tags={"Parkings"},
     *      summary="Row List of parking",
     *      description="Row List of parking",
     *      security={{"sanctum": {}}},
     *      operationId="indexParkingsRows",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Row list of Parking Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Parking $parking
     * @return JsonResponse
     */
    public function index(Parking $parking): JsonResponse
    {
        $parkings = $this->rowService->findAllByParking($parking);

        return $this->successResponse($parkings);
    }
}
