<?php

namespace App\Http\Controllers\Api\v1\Block;

use App\Models\Block;
use App\Http\Controllers\ApiController;
use App\Services\Application\Row\RowService;
use Illuminate\Http\JsonResponse;

class BlockRowController extends ApiController
{
    /**
     * @var RowService
     */
    private $rowService;

    public function __construct(
        RowService $rowService
    ){
        $this->middleware('role:Super-Admin|admin');
        $this->rowService = $rowService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/blocks/{id}/rows",
     *      tags={"Blocks"},
     *      summary="Row List of block",
     *      description="Row List of block",
     *      security={{"sanctum": {}}},
     *      operationId="indexBlocksRows",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(response=200, description="Row list of Block Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Block $block
     * @return JsonResponse
     */
    public function index(Block $block): JsonResponse
    {
        $rows = $this->rowService->findAllByBlock($block);

        return $this->showAll($rows);
    }
}
