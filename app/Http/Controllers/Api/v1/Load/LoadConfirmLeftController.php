<?php

namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Models\Load;
use Illuminate\Http\JsonResponse;

class LoadConfirmLeftController extends ApiController
{

    /**
     * @param Load $load
     * @return JsonResponse
     */
    public function confirmLeft(Load $load): JsonResponse
    {
        return $this->showMessage("Salida de load confirmada.");
    }
}
