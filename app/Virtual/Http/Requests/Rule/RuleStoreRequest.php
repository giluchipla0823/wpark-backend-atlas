<?php

namespace App\Virtual\Http\Requests\Rule;

/**
 * @OA\Schema(
 *      title="Rule Store Request",
 *      description="Rule Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RuleStoreRequest"
 *      ),
 *      required={"name", "code", "priority", "blocks", "conditions"}
 * )
 */
class RuleStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la regla",
     *     example="FORD"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="priority",
     *     type="integer",
     *     maxLength=10,
     *     description="Indica el orden de prioridad de la regla",
     *     example="1"
     * )
     */
    public $priority;

    /**
     * @OA\Property(
     *     property="blocks",
     *     type="array",
     *     @OA\Items(type="integer"),
     *     description="Id´s de los bloques que irán asociados a la regla"
     * )
     */
    public $blocks;

    // TODO: Ver como añadir un array dentro del array
    /**
     * @OA\Property(
     *     property="conditions",
     *     type="array",
     *     @OA\Items(type="integer"),
     *     description="Id´s de las condiciones que irán asociadas a la regla"
     * )
     */
    public $conditions;
}
