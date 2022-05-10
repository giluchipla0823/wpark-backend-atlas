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

    /**
     * @OA\Property(
     *     property="presorting_default",
     *     type="boolean",
     *     description="Si el bloque es tipo presorting o normal",
     *     example="false"
     * )
     */
    public $presorting_default;

}
