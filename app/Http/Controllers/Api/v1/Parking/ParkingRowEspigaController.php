<?php

namespace App\Http\Controllers\Api\v1\Parking;

use App\Http\Controllers\ApiController;
use App\Models\Parking;
use App\Services\Application\Row\RowService;
use Illuminate\Http\JsonResponse;

class ParkingRowEspigaController extends ApiController
{
    /**
     * @var RowService
     */
    private $rowService;

    public function __construct(
        RowService $rowService
    )
    {
        $this->rowService = $rowService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/parkings/{id}/row-espigas",
     *      tags={"Parkings"},
     *      summary="Rows list of spykes Parkings",
     *      description="Rows list of spykes Parkings",
     *      security={{"sanctum": {}}},
     *      operationId="listSpykesParkingsRows",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Row from spikes Parking list Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * List of rows from spikes parking.
     *
     * @param Parking $parking
     * @return JsonResponse
     */
    public function rowsSpikes(Parking $parking): JsonResponse
    {
        $results = $this->rowService->findAllBySpykesParking($parking);

        return $this->successResponse($results);
    }

}
