<?php

namespace App\Virtual\Http\Requests\Vehicle;

/**
 * @OA\Schema(
 *      title="Vehicle Manual Store Request",
 *      description="Vehicle Manual Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="VehicleManualStoreRequest"
 *      ),
 *      required={"vin", "lvin", "vin_short", "eoc", "design_id", "color_id", "destination_code_id", "entry_transport_id", "parking_id", "created_from"}
 * )
 */
class VehicleManualStoreRequest
{
    /**
     * @OA\Property(
     *     property="vin",
     *     type="string",
     *     minLength=17,
     *     maxLength=17,
     *     description="Vin del vehículo",
     *     example="NM0GE9E20N1514931"
     * )
     */
    public $vin;

    /**
     * @OA\Property(
     *     property="lvin",
     *     type="string",
     *     minLength=17,
     *     maxLength=17,
     *     description="Vin del vehículo",
     *     example="NM0GE9E20N1514931"
     * )
     */
    public $lvin;

    /**
     * @OA\Property(
     *     property="vin_short",
     *     type="string",
     *     minLength=7,
     *     maxLength=7,
     *     description="Vin short del vehículo",
     *     example="20N7777"
     * )
     */
    public $vin_short;

    /**
     * @OA\Property(
     *     property="eoc",
     *     type="string",
     *     maxLength=80,
     *     description="EOC del vehículo",
     *     example="50CS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD EEEIAC 2P5 BP 2"
     * )
     */
    public $eoc;

    /**
     * @OA\Property(
     *     property="design_id",
     *     type="integer",
     *     description="Id del modelo del vehículo",
     *     example="1"
     * )
     */
    public $design_id;

    /**
     * @OA\Property(
     *     property="color_id",
     *     type="integer",
     *     description="Id del color del vehículo",
     *     example="1"
     * )
     */
    public $color_id;

    /**
     * @OA\Property(
     *     property="destination_code_id",
     *     type="integer",
     *     description="Id del código de destino del vehículo",
     *     example="1"
     * )
     */
    public $destination_code_id;

    /**
     * @OA\Property(
     *     property="entry_transport_id",
     *     type="integer",
     *     description="Id del método de transporte de entrada del vehículo",
     *     example="1"
     * )
     */
    public $entry_transport_id;

    /**
     * @OA\Property(
     *     property="info",
     *     type="string",
     *     description="Información adicional del vehículo",
     *     example=""
     * )
     */
    public $info;

    /**
     * @OA\Property(
     *     property="parking_id",
     *     type="integer",
     *     description="Id de parking donde se coloca el vehículo.",
     *     example="1"
     * )
     */
    public $parking_id;

    /**
     * @OA\Property(
     *     property="created_from",
     *     type="string",
     *     description="Desde que aplicación fue creado del vehículo",
     *     example="mobile"
     * )
     */
    public $created_from;
}
