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
 *      required={"name", "code", "route_id", "country_id"}
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
     *     property="route_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la ruta del código de destino",
     *     example="1"
     * )
     */
    public $route_id;

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

}
