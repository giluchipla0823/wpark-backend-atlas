<?php

namespace App\Http\Controllers\Api\v1\Block;

use App\Http\Controllers\ApiController;
use App\Models\Block;
use App\Models\Row;
use App\Services\Row\RowService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @OA\Patch(
     *      path="/api/v1/blocks/{block}/rows/{row}/unlink",
     *      tags={"Blocks"},
     *      summary="Unlink block of row",
     *      description="Unlink block of row",
     *      security={{"sanctum": {}}},
     *      operationId="unlinkBlocksRows",
     *      @OA\Parameter(
     *          name="block",
     *          in="path",
     *          required=true,
     *          description="ID of block",
     *          example="1"
     *      ),
     *      @OA\Parameter(
     *          name="row",
     *          in="path",
     *          required=true,
     *          description="ID of row",
     *          example="1"
     *      ),
     *      @OA\Response(response=200, description="Unlink Block Successfully"),
     *      @OA\Response(
     *          response=400,
     *          description="The selected row is not in the specified block."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Block or Row not found."
     *      ),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Unlink block of Row.
     *
     * @param Block $block
     * @param Row $row
     * @return JsonResponse
     * @throws Exception
     */
    public function unlink(Block $block, Row $row): JsonResponse
    {
        if (!$block->rows->find($row)) {
            throw new Exception(
                "La fila seleccionada no se encuentra en el bloque {$block->name}.",
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->rowService->unlinkBlock($row);

        return $this->showMessage("Se ha eliminado la fila del bloque {$block->name}.");
    }


}
