<?php

namespace App\Virtual\Http\Requests\Parking;

/**
 * @OA\Schema(
 *      title="ParkingType Store Request",
 *      description="ParkingType Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ParkingTypeStoreRequest"
 *      ),
 *      required={"name"}
 * )
 */
class ParkingTypeStoreRequest
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
