<?php

namespace App\Virtual\Http\Requests\Hold;

/**
 * @OA\Schema(
 *      title="Hold Store Request",
 *      description="Hold Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="HoldStoreRequest"
 *      ),
 *      required={"name", "code", "priority", "conditions"}
 * )
 */
class HoldStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del bloqueo",
     *     example="STOLEN"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=255,
     *     description="Código del bloqueo",
     *     example="AZ"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="priority",
     *     type="integer",
     *     maxLength=10,
     *     description="Indica el orden de prioridad del bloqueo",
     *     example="1"
     * )
     */
    public $priority;

    /**
     * @OA\Property(
     *     property="conditions",
     *     type="array",
     *     @OA\Items(type="integer"),
     *     description="Id´s de las condiciones que irán asociadas al bloqueo"
     * )
     */
    public $conditions;
}
