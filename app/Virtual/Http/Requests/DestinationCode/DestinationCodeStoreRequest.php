<?php

namespace App\Virtual\Http\Requests\DestinationCode;

/**
 * @OA\Schema(
 *      title="Destination Code Store Request",
 *      description="Destination Code Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="DestinationCodeStoreRequest"
 *      ),
 *      required={"name", "code", "country_id"}
 * )
 */
class DestinationCodeStoreRequest
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
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción del código de destino",
     *     example="Código de destino para Italia"
     * )
     */
    public $description;

}
