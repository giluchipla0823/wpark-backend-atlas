<?php

namespace App\Virtual\Http\Requests\Compound;

/**
 * @OA\Schema(
 *      title="Compound Update Request",
 *      description="Compound Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="CompoundUpdateRequest"
 *      ),
 *      required={"name"}
 * )
 */
class CompoundUpdateRequest
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
