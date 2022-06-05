<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Cancel Request",
 *      description="Movement Cancel request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementCancelRequest"
 *      ),
 * )
 */
class MovementCancelRequest
{
    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el movimiento",
     *     example="Movimiento cancelado por bloqueo"
     * )
     */
    public $comments;

}
