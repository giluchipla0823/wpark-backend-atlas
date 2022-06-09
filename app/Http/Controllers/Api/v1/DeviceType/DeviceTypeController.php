<?php

namespace App\Http\Controllers\Api\v1\DeviceType;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Services\Application\DeviceType\DeviceTypeService;

class DeviceTypeController extends ApiController
{
    /**
     * @var DeviceTypeService
     */
    private $deviceTypeService;

    public function __construct(
        DeviceTypeService $deviceTypeService
    )
    {
        $this->deviceTypeService = $deviceTypeService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/devices-types",
     *      tags={"DevicesTypes"},
     *      summary="Devices Types List",
     *      description="List of devices types",
     *      operationId="indexDevicesTypes",
     *      @OA\Response(response=200, description="Devices Types list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $results = $this->deviceTypeService->findAll($request);

        return $this->showAll($results);
    }
}
