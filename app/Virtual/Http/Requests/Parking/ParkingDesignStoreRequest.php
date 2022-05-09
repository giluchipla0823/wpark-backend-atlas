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
 *      required={"name", "area_id", "parking_type_id", "rows", "qr"}
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
     *     example="2"
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
     *     property="rows",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="slots",
     *               type="integer",
     *               example="10",
     *          ),
     *          @OA\Property(
     *               property="block_id",
     *               type="integer",
     *               example="2"
     *          ),
     *     ),
     *     @OA\Schema(type="object"),
     *     description="Filas con sus propiedades",
     *     example={
     *          {
     *           "slots": 10,
     *           "block_id": 2
     *          },
     *          {
     *           "slots": 10,
     *           "block_id": 2
     *          },
     *          {
     *           "slots": 10,
     *           "block_id": null
     *          },
     *          {
     *           "slots": 10,
     *           "block_id": 2
     *          },
     *          {
     *           "slots": 10,
     *           "block_id": 2
     *          },
     *     }
     * )
     */
    public $rows;

    /**
     * @OA\Property(
     *     property="qr",
     *     type="integer",
     *     maxLength=10,
     *     description="Parte izquierda del código qr pertenciente al parking",
     *     example="22"
     * )
     */
    public $qr;

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
