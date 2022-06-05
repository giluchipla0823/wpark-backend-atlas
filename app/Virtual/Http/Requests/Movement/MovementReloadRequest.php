<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Reload Request",
 *      description="Movement Reload request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementReloadRequest"
 *      ),
 *      required={"previous_movement_id", "vehicle_id", "action"}
 * )
 */
class MovementReloadRequest
{
    /**
     * @OA\Property(
     *     property="previous_movement_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el movimiento anterior del vehículo",
     *     example="1"
     * )
     */
    public $previous_movement_id;

    /**
     * @OA\Property(
     *     property="vehicle_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el vehículo sobre el que se va a hacer la recomendación",
     *     example="1"
     * )
     */
    public $vehicle_id;

    /**
     * @OA\Property(
     *     property="action",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el tipo de acción que se va a realizar en la recomendación",
     *     example="1"
     * )
     */
    public $action;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el movimiento",
     *     example="Movimiento cancelado por reload"
     * )
     */
    public $comments;

}
