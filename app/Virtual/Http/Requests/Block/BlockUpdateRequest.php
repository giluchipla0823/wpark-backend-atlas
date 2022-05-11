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
 *      required={"name"}
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

}
