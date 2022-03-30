<?php

namespace App\Virtual\Http\Requests\Color;

/**
 * @OA\Schema(
 *      title="Color Update Request",
 *      description="Color Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ColorUpdateRequest"
 *      ),
 *      required={"name", "code", "simple_name", "hex"}
 * )
 */
class ColorUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del color",
     *     example="FROZEN WHITE"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=255,
     *     description="Código del color",
     *     example="FRWH"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="simple_name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre corto del color",
     *     example="WHITE"
     * )
     */
    public $simple_name;

    /**
     * @OA\Property(
     *     property="hex",
     *     type="string",
     *     maxLength=255,
     *     description="Hexadecimal del color",
     *     example="#ffffff"
     * )
     */
    public $hex;
}
