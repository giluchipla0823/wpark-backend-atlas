<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Models\Load;
use App\Services\Application\Load\LoadService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoadConfirmLeftController extends ApiController
{
    /**
     * @var LoadService
     */
    private $loadService;

    public function __construct(
        LoadService $loadService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->loadService = $loadService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/loads/{load}/confirm-left",
     *      tags={"Loads"},
     *      summary="Confirm Left",
     *      description="Confirm left of load",
     *      security={{"sanctum": {}}},
     *      operationId="confirmLeftLoad",
     *      @OA\Response(response=200, description="Confirmed load output"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Confirm left of load
     *
     * @param Load $load
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(Load $load): JsonResponse
    {
        $this->loadService->confirmLeft($load);
        return $this->showMessage("Salida de load confirmada.");
    }
}
