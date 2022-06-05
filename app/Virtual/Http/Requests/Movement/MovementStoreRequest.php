<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Store Request",
 *      description="Movement Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementStoreRequest"
 *      ),
 *      required={"vehicle_id", "origin_position_type", "origin_position_id", "destination_position_type", "destination_position_id"}
 * )
 */
class MovementStoreRequest
{
    /**
     * @OA\Property(
     *     property="vehicle_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el vehículo que se mueve",
     *     example="1"
     * )
     */
    public $vehicle_id;

    /**
     * @OA\Property(
     *     property="origin_position_type",
     *     type="string",
     *     maxLength=255,
     *     description="Indica el tipo de posición slot o parking de origen",
     *     example="App\Models\Parking"
     * )
     */
    public $origin_position_type;

    /**
     * @OA\Property(
     *     property="origin_position_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la posición desde donde se hace el movimiento",
     *     example="1"
     * )
     */
    public $origin_position_id;

    /**
     * @OA\Property(
     *     property="destination_position_type",
     *     type="string",
     *     maxLength=255,
     *     description="Indica el tipo de posición slot o parking de destino",
     *     example="App\Models\Slot"
     * )
     */
    public $destination_position_type;

    /**
     * @OA\Property(
     *     property="destination_position_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la posición haciá donde se hace el movimiento",
     *     example="2"
     * )
     */
    public $destination_position_id;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el movimiento",
     *     example="Movimiento cancelado por bloqueo"
     * )
     */
    public $comments;

}
