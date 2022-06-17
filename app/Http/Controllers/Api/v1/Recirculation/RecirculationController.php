<?php

namespace App\Http\Controllers\Api\v1\Recirculation;

use App\Http\Controllers\ApiController;
use App\Models\Recirculation;
use App\Services\Application\Recirculation\RecirculationService;
use Illuminate\Http\JsonResponse;

class RecirculationController extends ApiController
{
    /**
     * @var RecirculationService
     */
    private $recirculationService;

    public function __construct(
        RecirculationService $recirculationService
    )
    {
        $this->recirculationService = $recirculationService;
    }

    /**
     * @param Recirculation $recirculation
     * @return JsonResponse
     */
    public function updateBack(Recirculation $recirculation): JsonResponse
    {
        $this->recirculationService->updateBack($recirculation);

        return $this->showMessage("Recirculation update back successfully");
    }
}
