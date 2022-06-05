<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Filtered Positions Request",
 *      description="Movement Filtered Positions request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementFilteredPositionsRequest"
 *      ),
 *      required={"vehicle_id", "parking_id"}
 * )
 */
class MovementFilteredPositionsRequest
{
    /**
     * @OA\Property(
     *     property="vehicle_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el vehículo que se va a mover",
     *     example="1"
     * )
     */
    public $vehicle_id;

    /**
     * @OA\Property(
     *     property="parking_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el parking de destino",
     *     example="2"
     * )
     */
    public $parking_id;

}
