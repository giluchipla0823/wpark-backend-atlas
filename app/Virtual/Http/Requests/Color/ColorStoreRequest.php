<?php

namespace App\Virtual\Http\Requests\Color;

/**
 * @OA\Schema(
 *      title="Color Store Request",
 *      description="Color Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ColorStoreRequest"
 *      ),
 *      required={"name", "code", "simple_name", "hex"}
 * )
 */
class ColorStoreRequest
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
