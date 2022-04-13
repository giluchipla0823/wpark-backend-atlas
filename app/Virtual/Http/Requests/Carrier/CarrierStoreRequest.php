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
 *      required={"name", "code", "is_train"}
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
     *     example="TRANSFESA"
     * )
     */
    public $name;

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
     *     property="is_train",
     *     type="integer",
     *     description="¿Es tren?",
     *     example="1"
     * )
     */
    public $is_train;
}
