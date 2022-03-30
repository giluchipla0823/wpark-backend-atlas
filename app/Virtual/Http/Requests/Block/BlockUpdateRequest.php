<?php

namespace App\Virtual\Http\Requests\Block;

/**
 * @OA\Schema(
 *      title="Block Update Request",
 *      description="Block Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="BlockUpdateRequest"
 *      ),
 *      required={"name", "active"}
 * )
 */
class BlockUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del bloque",
     *     example="BLOQUE ZP"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el bloque está activo (0: No está activo, 1: Está activo)",
     *     example="1"
     * )
     */
    public $active;
}
