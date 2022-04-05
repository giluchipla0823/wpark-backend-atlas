<?php

namespace App\Virtual\Http\Requests\State;

/**
 * @OA\Schema(
 *      title="State Update Request",
 *      description="State Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="StateUpdateRequest"
 *      ),
 *      required={"name", "model_state_id"}
 * )
 */
class StateUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del estado",
     *     example="STATIONED"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción del estado",
     *     example="El vehículo está estacionado"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="model_state_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica si el estado será para un vehículo o para una fila",
     *     example="1"
     * )
     */
    public $model_state_id;
}