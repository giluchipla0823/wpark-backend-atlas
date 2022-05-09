<?php

namespace App\Virtual\Http\Requests\Carrier;

/**
 * @OA\Schema(
 *      title="Carrier Store Request",
 *      description="Carrier Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="CarrierStoreRequest"
 *      ),
 *      required={"name", "short_name", "code"}
 * )
 */
class CarrierStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del transportista",
     *     example="SINTAX LOGISTICA SA"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="short_name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre corto del transportista",
     *     example="SINTAX"
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
     *     example="BKT9A"
     * )
     */
    public $code;

}
