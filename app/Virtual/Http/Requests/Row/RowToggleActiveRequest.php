<?php

namespace App\Virtual\Http\Requests\Row;

/**
 * @OA\Schema(
 *      title="Row Toggle Active Request",
 *      description="Row Toggle Active request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RowToggleActiveRequest"
 *      )
 * )
 */
class RowToggleActiveRequest
{

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre la fila",
     *     example="La fila está activada/desactivada"
     * )
     */
    public $comments;
}
