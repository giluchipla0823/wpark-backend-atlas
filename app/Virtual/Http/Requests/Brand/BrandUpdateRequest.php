<?php

namespace App\Virtual\Http\Requests\Brand;

/**
 * @OA\Schema(
 *      title="Brand Update Request",
 *      description="Brand Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="BrandUpdateRequest"
 *      ),
 *      required={"name", "code", "compound_id"}
 * )
 */
class BrandUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la marca",
     *     example="FORD"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=255,
     *     description="Código de la marca",
     *     example="12"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="compound_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la campa asociada a la marca",
     *     example="1"
     * )
     */
    public $compound_id;
}
