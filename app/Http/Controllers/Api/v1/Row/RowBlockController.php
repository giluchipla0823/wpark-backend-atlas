<?php

namespace App\Http\Controllers\Api\v1\Row;

use Exception;
use App\Models\Row;
use App\Models\Block;
use App\Http\Controllers\ApiController;;
use App\Services\Application\Row\RowService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RowBlockController extends ApiController
{

    /**
     * @var RowService
     */
    private $rowService;

    public function __construct(
        RowService $rowService
    )
    {
        $this->rowService = $rowService;
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/rows/{row}/blocks/{block}",
     *      tags={"Rows"},
     *      summary="Update block to row",
     *      description="Update block to row",
     *      security={{"sanctum": {}}},
     *      operationId="updateRowsBlocks",
     *      @OA\Parameter(
     *          name="row",
     *          in="path",
     *          required=true,
     *          description="ID of row",
     *          example="1"
     *      ),
     *     @OA\Parameter(
     *          name="block",
     *          in="path",
     *          required=true,
     *          description="ID of block",
     *          example="1"
     *      ),
     *      @OA\Response(response=200, description="Update Block to Row Successfully"),
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
     * @param Row $row
     * @param Block $block
     * @return JsonResponse
     */
    public function update(Row $row, Block $block): JsonResponse
    {
        $this->rowService->updateBlock($row, $block);

        return $this->showMessage('El block se actualizó correctamente.');
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/rows/{row}/blocks/unlink",
     *      tags={"Rows"},
     *      summary="Unlink block of row",
     *      description="Unlink block of row",
     *      security={{"sanctum": {}}},
     *      operationId="unlinkRowsBlocks",
     *      @OA\Parameter(
     *          name="row",
     *          in="path",
     *          required=true,
     *          description="ID of row",
     *          example="1"
     *      ),
     *      @OA\Response(response=200, description="Unlink Block of Row Successfully"),
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
     * @param Row $row
     * @return JsonResponse
     * @throws Exception
     */
    public function unlink(Row $row): JsonResponse
    {
        if (!$row->block) {
            throw new Exception(
                "La fila seleccionada no tiene asignado un bloque.",
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->rowService->unlinkBlock($row);

        return $this->showMessage("Se ha eliminado el bloque de la fila seleccionada.");
    }
}
