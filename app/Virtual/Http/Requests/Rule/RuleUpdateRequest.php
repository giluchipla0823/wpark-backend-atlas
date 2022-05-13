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
     *     property="priority",
     *     type="integer",
     *     maxLength=10,
     *     description="Indica el orden de prioridad de la regla",
     *     example="1"
     * )
     */
    public $priority; // Activar solo si es una regla simple

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
     *     property="predefined_zone_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el parking que va asociado la regla",
     *     example="2"
     * )
     */
    //public $predefined_zone_id; // Activar solo si es regla simple y va a asociado a un parking

    /**
     * @OA\Property(
     *     property="carrier_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el transportista por defecto que va asociado la regla",
     *     example="2"
     * )
     */
    public $carrier_id; // Activar solo si es regla simple

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

    /**
     * @OA\Property(
     *     property="rules",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="rule_id",
     *               type="integer",
     *               example="2"
     *     ),
     *     @OA\Items(type="array"),
     *     description="Id´s de las reglas que irán asociadas al grupo de reglas"
     * )
     */
    //public $rules; // Activar solo si es regla agrupada

    /**
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si la regla está activa (0: No está activa, 1: Está activa)",
     *     example="1"
     * )
     */
    public $active;

}
