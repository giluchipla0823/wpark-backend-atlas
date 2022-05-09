<?php

namespace App\Virtual\Http\Requests\Carrier;

/**
 * @OA\Schema(
 *      title="Carrier Update Request",
 *      description="Carrier Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="CarrierUpdateRequest"
 *      ),
 *      required={"name", "short_name", "code", "active"}
 * )
 */
class CarrierUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del transportista",
     *     example="TRANSFESA LOGÍSTICA S.L."
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="short_name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre corto del transportista",
     *     example="TRANSFESA"
     * )
     */
    public $short_name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     minLength=3,
     *     maxLength=10,
     *     description="Código del transportista",
     *     example="TRANS"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el transportista está activo (0: No está activo, 1: Está activo)",
     *     example="1"
     * )
     */
    public $active;
}
