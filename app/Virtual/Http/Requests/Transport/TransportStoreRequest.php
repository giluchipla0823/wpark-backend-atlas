<?php

namespace App\Virtual\Http\Requests\Transport;

/**
 * @OA\Schema(
 *      title="Transport Store Request",
 *      description="Transport Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="TransportStoreRequest"
 *      ),
 *      required={"name", "import"}
 * )
 */
class TransportStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del transporte",
     *     example="BOAT"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="import",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el vehículo es importado (0: No es importado, 1: Es importado)",
     *     example="0"
     * )
     */
    public $import;

}
