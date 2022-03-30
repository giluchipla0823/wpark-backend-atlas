<?php

namespace App\Virtual\Http\Requests\Parking;

/**
 * @OA\Schema(
 *      title="Parking Design Store Request",
 *      description="Parking Design Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ParkingDesignStoreRequest"
 *      ),
 *      required={"name", "area_id", "parking_type_id", "start_row", "end_row"}
 * )
 */
class ParkingDesignStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del parking",
     *     example="PU"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="area_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el área al que pertenece el parking",
     *     example="1"
     * )
     */
    public $area_id;

    /**
     * @OA\Property(
     *     property="parking_type_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el tipo de parking",
     *     example="1"
     * )
     */
    public $parking_type_id;

    /**
     * @OA\Property(
     *     property="start_row",
     *     type="integer",
     *     maxLength=10,
     *     description="La fila del área en la que empieza el parking",
     *     example="12"
     * )
     */
    public $start_row;

    /**
     * @OA\Property(
     *     property="end_row",
     *     type="integer",
     *     maxLength=10,
     *     description="La fila del área en la que termina el parking",
     *     example="22"
     * )
     */
    public $end_row;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el parking",
     *     example="Este parking deberá cambiar de ubicación"
     * )
     */
    public $comments;
}
