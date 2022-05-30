<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Recommend Request",
 *      description="Movement Recommend request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementRecommendRequest"
 *      ),
 *      required={"vehicle_id", "action"}
 * )
 */
class MovementRecommendRequest
{
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

}
