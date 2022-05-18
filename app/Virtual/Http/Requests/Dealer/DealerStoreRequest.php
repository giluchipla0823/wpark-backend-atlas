<?php

namespace App\Virtual\Http\Requests\Dealer;

/**
 * @OA\Schema(
 *      title="Dealer Store Request",
 *      description="Dealer Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="DealerStoreRequest"
 *      ),
 *      required={"name", "code", "zip_code", "city", "street", "country"}
 * )
 */
class DealerStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del distribuidor",
     *     example="MOTOREBRE  S.A."
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=255,
     *     description="Código del distribuidor",
     *     example="1045V"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="zip_code",
     *     type="string",
     *     maxLength=255,
     *     description="Código postal del distribuidor",
     *     example="43870"
     * )
     */
    public $zip_code;

    /**
     * @OA\Property(
     *     property="city",
     *     type="string",
     *     maxLength=255,
     *     description="Ciudad del distribuidor",
     *     example="AMPOSTA"
     * )
     */
    public $city;

    /**
     * @OA\Property(
     *     property="street",
     *     type="string",
     *     maxLength=255,
     *     description="Dirección del distribuidor",
     *     example="AVINGUDA DE SANT JAUME, S/N"
     * )
     */
    public $street;

    /**
     * @OA\Property(
     *     property="country",
     *     type="string",
     *     maxLength=255,
     *     description="Pais del distribuidor",
     *     example="España"
     * )
     */
    public $country;

}
