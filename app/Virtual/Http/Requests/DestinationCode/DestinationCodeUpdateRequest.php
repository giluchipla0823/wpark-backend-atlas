<?php

namespace App\Virtual\Http\Requests\DestinationCode;

/**
 * @OA\Schema(
 *      title="Destination Code Update Request",
 *      description="Destination Code Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="DestinationCodeUpdateRequest"
 *      ),
 *      required={"name", "code", "country_id", "active"}
 * )
 */
class DestinationCodeUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del código de destino",
     *     example="ANTWERP_CHINA"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=5,
     *     description="Código del código de destino",
     *     example="AC"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="country_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el país del código de destino",
     *     example="1"
     * )
     */
    public $country_id;

    /**
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el código de destino está activo (0: No está activo, 1: Está activo)",
     *     example="1"
     * )
     */
    public $active;
}
