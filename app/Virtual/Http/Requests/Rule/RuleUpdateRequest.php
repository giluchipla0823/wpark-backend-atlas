<?php

namespace App\Virtual\Http\Requests\Rule;

/**
 * @OA\Schema(
 *      title="Rule Update Request",
 *      description="Rule Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RuleUpdateRequest"
 *      ),
 *      required={"name", "is_group", "active"}
 * )
 */
class RuleUpdateRequest
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
     *     property="is_group",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si es una regla simple o un grupo de reglas (0: Regla simple, 1: Grupo de reglas)",
     *     example="0"
     * )
     */
    public $is_group;

    /**
     * @OA\Property(
     *     property="block_id",
     *     type="integer",
     *     maxLength=10,
     *     description="Indica el bloque que va asociado la regla",
     *     example="2"
     * )
     */
    public $block_id; // Activar solo si es regla simple y va a asociado a una fila

    /**
     * @OA\Property(
     *     property="conditions",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="condition_id",
     *               type="integer",
     *               example="3"
     *          ),
     *          @OA\Property(
     *               property="conditionable_type",
     *               type="string",
     *               example="App\Models\DestinationCode"
     *          ),
     *          @OA\Property(
     *               property="conditionable_id",
     *               type="integer",
     *               example="2"
     *          ),
     *     ),
     *     @OA\Items(type="array"),
     *     description="Id´s de las condiciones que irán asociadas a la regla"
     * )
     */
    public $conditions; // Activar solo si es regla simple
}
