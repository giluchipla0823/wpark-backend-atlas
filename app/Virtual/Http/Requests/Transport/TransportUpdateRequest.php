<?php

namespace App\Virtual\Http\Requests\Transport;

/**
 * @OA\Schema(
 *      title="Transport Update Request",
 *      description="Transport Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="TransportUpdateRequest"
 *      ),
 *      required={"name", "import"}
 * )
 */
class TransportUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del transporte",
     *     example="PLANE"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="import",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el vehículo es importado (0: No es importado, 1: Es importado)",
     *     example="1"
     * )
     */
    public $import;

}
