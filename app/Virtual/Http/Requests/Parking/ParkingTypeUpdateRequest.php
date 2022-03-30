<?php

namespace App\Virtual\Http\Requests\Parking;

/**
 * @OA\Schema(
 *      title="ParkingType Update Request",
 *      description="ParkingType Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ParkingTypeUpdateRequest"
 *      ),
 *      required={"name"}
 * )
 */
class ParkingTypeUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del tipo de parking",
     *     example="ESPIGA"
     * )
     */
    public $name;
}
