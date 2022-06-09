<?php

namespace App\Http\Controllers\Api\v1\Row;

use App\Exceptions\owner\BadRequestException;
use App\Exceptions\owner\NotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Row\RowRellocateRequest;
use App\Models\Row;
use App\Services\Application\Row\RowRellocateService;
use Illuminate\Http\JsonResponse;

class RowRellocateController extends ApiController
{
    /**
     * @var RowRellocateService
     */
    private $movementRowRellocateService;

    public function __construct(
        RowRellocateService $movementRowRellocateService
    )
    {
        $this->movementRowRellocateService = $movementRowRellocateService;
    }

    /**
    * /**
     * @OA\PUT(
     *     path="/api/v1/rows/{id}/rellocate",
     *     tags={"Rows"},
     *     summary="Row Rellocate",
     *     description="Row Rellocate",
     *     security={{"sanctum": {}}},
     *     operationId="rowRellocate",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RowRellocateRequest")
     *     ),
     *     @OA\Response(response=200, description="Row Rellocate Successfully" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param Row $row
     * @param RowRellocateRequest $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function __invoke(Row $row, RowRellocateRequest $request): JsonResponse
    {
        $this->movementRowRellocateService->process($row, $request->all());

        return $this->showMessage("Row rellocated successfully.");
    }
}
