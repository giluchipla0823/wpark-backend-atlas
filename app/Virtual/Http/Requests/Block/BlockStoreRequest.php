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
 *      required={"name", "is_presorting"}
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

    /**
     * @OA\Property(
     *     property="is_presorting",
     *     type="boolean",
     *     description="Si el bloque es tipo presorting o normal",
     *     example="false"
     * )
     */
    public $is_presorting;

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
