<?php

namespace App\Virtual\Http\Requests\Condition;

/**
 * @OA\Schema(
 *      title="Condition Store Request",
 *      description="Condition Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ConditionStoreRequest"
 *      ),
 *      required={"name", "model_condition_id", "required"}
 * )
 */
class ConditionStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la condición",
     *     example="CÓDIGO DESTINO"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción de la condición",
     *     example="Condición por códigos de destino"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="model_condition_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica si la condición será para un hold o para una regla",
     *     example="1"
     * )
     */
    public $model_condition_id;

    /**
     * @OA\Property(
     *     property="required",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si la condición es obligatoria (0: No es obligatoria, 1: Es obligatoria)",
     *     example="1"
     * )
     */
    public $required;
}
