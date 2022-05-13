<?php

namespace App\Http\Controllers\Api\v1\Parking;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Parking\ParkingDesignStoreRequest;
use App\Services\Application\Parking\ParkingDesignService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ParkingDesignController extends ApiController
{
    /**
     * @var ParkingDesignService
     */
    private $parkingDesignService;

    public function __construct(
        ParkingDesignService $parkingDesignService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->parkingDesignService = $parkingDesignService;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/parking-design",
     *     tags={"ParkingDesigns"},
     *     summary="Create New Parking",
     *     description="Create New Parking and all associated rows and slots",
     *     security={{"sanctum": {} }},
     *     operationId="storeParkingDesign",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ParkingDesignStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New ParkingDesign" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ParkingDesignStoreRequest $request
     * @return JsonResponse
     */
    public function parkingDesign(ParkingDesignStoreRequest $request): JsonResponse
    {
        $parking = $this->parkingDesignService->parkingDesign($request->all());

        return $this->successResponse($parking, 'Parking created successfully.', Response::HTTP_CREATED);
    }

}
