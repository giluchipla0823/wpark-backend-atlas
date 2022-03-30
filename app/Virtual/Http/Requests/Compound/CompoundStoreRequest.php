<?php

namespace App\Virtual\Http\Requests\Compound;

/**
 * @OA\Schema(
 *      title="Compound Store Request",
 *      description="Compound Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="CompoundStoreRequest"
 *      ),
 *      required={"name"}
 * )
 */
class CompoundStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la campa",
     *     example="FORD VALENCIA"
     * )
     */
    public $name;
}
