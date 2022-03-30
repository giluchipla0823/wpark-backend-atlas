<?php

namespace App\Virtual\Http\Requests\Block;

/**
 * @OA\Schema(
 *      title="Block Store Request",
 *      description="Block Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="BlockStoreRequest"
 *      ),
 *      required={"name"}
 * )
 */
class BlockStoreRequest
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
