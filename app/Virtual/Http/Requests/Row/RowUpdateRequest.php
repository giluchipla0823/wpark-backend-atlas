<?php

namespace App\Virtual\Http\Requests\Row;

/**
 * @OA\Schema(
 *      title="Row Update Request",
 *      description="Row Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RowUpdateRequest"
 *      ),
 *      required={"block_id", "alt_qr"}
 * )
 */
class RowUpdateRequest
{
    /**
     * @OA\Property(
     *     property="block_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el bloque al que pertenece la fila",
     *     example="1"
     * )
     */
    public $block_id;

    /**
     * @OA\Property(
     *     property="alt_qr",
     *     type="string",
     *     description="Código QR de la fila",
     *     example="022.001"
     * )
     */
    public $alt_qr;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre la fila",
     *     example="La fila está reservada"
     * )
     */
    public $comments;
}
