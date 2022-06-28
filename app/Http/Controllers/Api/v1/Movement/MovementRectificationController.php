<?php

namespace App\Http\Controllers\Api\v1\Movement;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Movement\MovementRectificationRequest;
use App\Models\Movement;
use App\Services\Application\Movement\MovementRectificationService;
use Illuminate\Http\JsonResponse;

class MovementRectificationController extends ApiController
{
    /**
     * @var MovementRectificationService
     */
    private $movementRectificationService;

    public function __construct(
        MovementRectificationService $movementRectificationService
    )
    {
        $this->movementRectificationService = $movementRectificationService;
    }

    /**
     * @param Movement $movement
     * @param MovementRectificationRequest $request
     * @return JsonResponse
     */
    public function update(Movement $movement, MovementRectificationRequest $request): JsonResponse
    {
        $this->movementRectificationService->process($movement, $request->all());

        return $this->successResponse($request->all());
    }
}
