<?php

namespace App\Http\Controllers\Api\v1\Device;

use App\Exceptions\owner\NotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Device\DeviceStoreRequest;
use App\Services\Application\Device\DeviceService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends ApiController
{
    /**
     * @var DeviceService
     */
    private $deviceService;

    public function __construct(
        DeviceService $deviceService
    )
    {
        $this->deviceService = $deviceService;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/devices",
     *     tags={"Devices"},
     *     summary="Create New Device",
     *     description="Create New Device",
     *     operationId="storeDevice",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/DeviceStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Device" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param DeviceStoreRequest $request
     * @return JsonResponse
     */
    public function store(DeviceStoreRequest $request): JsonResponse
    {
        $this->deviceService->store($request->all());

        return $this->showMessage("Devices created successfully.", Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/devices/search-by-uuid/{uuid}",
     *     tags={"Devices"},
     *     summary="Show Device Details",
     *     description="Show Device Details",
     *     operationId="searchByUuidDevice",
     *     @OA\Parameter(
     *          parameter="uuid",
     *          name="uuid",
     *          description="uuid, eg; 0545878751",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Show Device Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource with uuid.
     *
     * @param string $uuid
     * @return JsonResponse
     * @throws NotFoundException
     */
    public function searchByUuid(string $uuid): JsonResponse
    {
        $device = $this->deviceService->findOneOrFailByUuid($uuid);

        return $this->successResponse($device);
    }
}
